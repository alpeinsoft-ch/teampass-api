<?php

namespace Teampass\Api;

use PasswordLib\PasswordLib;
use Silex\Application as SilexApplication;
use Silex\Provider\DoctrineServiceProvider;
use Silex\Provider\TranslationServiceProvider;
use Silex\Provider\UrlGeneratorServiceProvider;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\Translation\Loader\YamlFileLoader;
use Teampass\Api\Provider\ApiServiceProvider;
use Teampass\Api\Provider\ConfigServiceProvider;
use Teampass\Api\Provider\PlatformServiceProvider;
use Teampass\Api\Repository\KeyRepository;
use Teampass\Api\Repository\NodeRepository;
use Teampass\Api\Repository\RepositoryContainer;
use Teampass\Api\Repository\UserRepository;

final class Application extends SilexApplication
{
    const DEFAULT_CONTROLLER_PATH = 'src/Controller';

    public function __construct(array $values = [])
    {
        parent::__construct($values);

        $this->configureParameters();
        $this->configureProviders();
        $this->configureServices();
        $this->configureListeners();
    }

    public function mountControllers()
    {
        $finder = (new Finder())
            ->in($this['root_dir'].'/'.self::DEFAULT_CONTROLLER_PATH)
            ->name('*Controller.php')
        ;
        foreach ($finder as $file) {
            /* @var \Symfony\Component\Finder\SplFileInfo $file */
            $class = 'Teampass\\Api\\Controller\\'.str_replace('.php', '', str_replace('/', '\\', $file->getRelativePathname()));
            $reflection = new \ReflectionClass($class);
            if ($reflection->isAbstract()) {
                continue;
            }
            $this->mount($this['config']['endpoint'].'/'.$this['config']['version'], new $class($this));
        }
    }

    private function configureParameters()
    {
        $this['root_dir'] = __DIR__.'/..';
    }

    private function configureProviders()
    {
        $this->register(new ConfigServiceProvider());
        $this->register(new UrlGeneratorServiceProvider());
        $this->register(new DoctrineServiceProvider(), $this['config']['database']);
        $this->register(new ApiServiceProvider());
        $this->register(new PlatformServiceProvider());
        $this->register(new TranslationServiceProvider(), [
            'locale_fallbacks' => [$this['config']['locale']],
        ]);
        $this['translator'] = $this->share($this->extend('translator', function ($translator) {
            /* @var \Symfony\Component\Translation\Translator $translator */
            $translator->addLoader('yaml', new YamlFileLoader());
            $translator->addResource('yaml', $this['root_dir'].'/app/translations/messages.en.yml', 'en');
            $translator->addResource('yaml', $this['root_dir'].'/app/translations/messages.de.yml', 'de');

            return $translator;
        }));
    }

    private function configureServices()
    {
        $app = $this;
        $this['repository.user'] = $this->share(function () use ($app) {
            return new UserRepository($app['db'], $app['repository_container']);
        });
        $this['repository.node'] = $this->share(function () use ($app) {
            return new NodeRepository($app['db'], $app['repository_container']);
        });
        $this['repository.key'] = $this->share(function () use ($app) {
            return new KeyRepository($app['db'], $app['repository_container']);
        });
        $this['repository_container'] = $this->share(function () use ($app) {
            return new RepositoryContainer($app, [
                'user' => 'repository.user',
                'node' => 'repository.node',
                'key' => 'repository.key',
                'platform.tree' => 'platform.tree',
                'platform.encoder' => 'platform.encoder',
                'encoder' => 'api.encoder',
            ]);
        });
    }

    private function configureListeners()
    {
        $this->before(function (Request $request) {
            $this['locale'] = $request->headers->get('Content-Language', $this['config']['locale']);

            if (0 === strpos($request->headers->get('Content-Type'), 'application/json')) {
                $data = json_decode($request->getContent(), true);
                $request->request->replace(is_array($data) ? $data : []);
            }
        });

        $this->before(function () {
            if (!isset($_SERVER['PHP_AUTH_USER'])) {
                throw new UnauthorizedHttpException('Teampass API', $this['translator']->trans('user.unauthorized', [], 'messages', $this['locale']));
            }

            $user = $this['repository.user']->findByLogin($_SERVER['PHP_AUTH_USER']);
            if (null === $user) {
                throw new AccessDeniedHttpException($this['translator']->trans('user.not_found', ['username' => $_SERVER['PHP_AUTH_USER']], 'messages', $this['locale']));
            }

            if (true == $user['disabled']) {
                throw new AccessDeniedHttpException($this['translator']->trans('user.disabled', ['username' => $_SERVER['PHP_AUTH_USER']], 'messages', $this['locale']));
            }

            $crypt = new PasswordLib();
            if (!$crypt->verifyPasswordHash($_SERVER['PHP_AUTH_PW'], $user['pw'])) {
                throw new AccessDeniedHttpException($this['translator']->trans('user.wrong_password', [], 'messages', $this['locale']));
            }

            $this['user'] = $user;
        });

        $this->error(function (\Exception $e, $code) {
            return new JsonResponse([
                'code' => $code,
                'message' => $e->getMessage(),
            ], $code, ['Content-Type' => 'application/problem+json']);
        });
    }
}
