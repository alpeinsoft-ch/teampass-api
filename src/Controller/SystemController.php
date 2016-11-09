<?php

namespace Teampass\Api\Controller;

use Silex\ControllerCollection;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Exception\InvalidParameterException;

final class SystemController extends AbstractController
{
    public function generatePassword(Request $request)
    {
        $data = 'POST' === $request->getMethod() ? $this->handleRequest($request) : $request->query->all();
        $generator = new \PWGen();
        $generator
            ->setCapitalize((bool) $data['upper_case'])
            ->setNumerals((bool) $data['numbers'])
            ->setSecure((bool) $data['secure'])
            ->setSymbols((bool) $data['symbols'])
            ->setLength((int) $data['length'])
        ;

        return new JsonResponse([
            'password' => $generator->generate(),
        ], 200);
    }

    public function complicationPassword()
    {
        return new JsonResponse([
            0 => $this->container['translator']->trans('complication.0', [], 'messages', $this->container['locale']),
            25 => $this->container['translator']->trans('complication.25', [], 'messages', $this->container['locale']),
            50 => $this->container['translator']->trans('complication.50', [], 'messages', $this->container['locale']),
            60 => $this->container['translator']->trans('complication.60', [], 'messages', $this->container['locale']),
            70 => $this->container['translator']->trans('complication.70', [], 'messages', $this->container['locale']),
            80 => $this->container['translator']->trans('complication.80', [], 'messages', $this->container['locale']),
            90 => $this->container['translator']->trans('complication.90', [], 'messages', $this->container['locale']),
        ], 200);
    }

    public function secret()
    {
        return new JsonResponse([
            'secret' => $this->container['config']['secret'],
        ], 200);
    }

    protected function addRoutes(ControllerCollection $controllers)
    {
        $controllers->get('/password/generate', [$this, 'generatePassword']);
        $controllers->post('/password/generate', [$this, 'generatePassword']);
        $controllers->get('/password/complication', [$this, 'complicationPassword']);
        $controllers->get('/secret', [$this, 'secret']);
    }

    private function handleRequest(Request $request)
    {
        $data = json_decode($request->getContent(), true);
        if (null === $data) {
            throw new InvalidParameterException($this->container['translator']->trans('invalid_body', [], 'messages', $this->container['locale']));
        }

        return $data;
    }
}
