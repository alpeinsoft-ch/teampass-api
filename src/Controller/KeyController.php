<?php

namespace Teampass\Api\Controller;

use Silex\ControllerCollection;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Exception\InvalidParameterException;

final class KeyController extends AbstractController
{
    protected function addRoutes(ControllerCollection $controllers)
    {
        $controllers->post('/key', [$this, 'create']);
        $controllers->get('/key/{id}', [$this, 'show']);
        $controllers->put('/key/{id}', [$this, 'update']);
        $controllers->delete('/key/{id}', [$this, 'delete']);
    }

    public function show($id)
    {
        $key = $this->container['repository.key']->findById($id, $this->container['user']);
        if (!$key) {
            throw new NotFoundHttpException($this->container['translator']->trans('key.not_found', [], 'messages', $this->container['locale']));
        }

        $node = $this->container['repository.node']->findById($key['folder'], $this->container['user']);
        if (!in_array('W', explode(', ', $node['access'])) && !in_array('R', explode(', ', $node['access']))) {
            throw new AccessDeniedHttpException($this->container['translator']->trans('node.not_read', [], 'messages', $this->container['locale']));
        }

        return new JsonResponse($key, 200);
    }

    public function create(Request $request)
    {
        $data = $this->handleRequest($request);
        $key = $this->container['repository.key']->findByLabel($data['label'], $this->container['user']);
        if ($key) {
            throw new ConflictHttpException($this->container['translator']->trans('key.already_exists', [], 'messages', $this->container['locale']));
        }

        $node = $this->container['repository.node']->findById($data['id_tree'], $this->container['user']);
        if (!$node) {
            throw new NotFoundHttpException($this->container['translator']->trans('node.not_found', [], 'messages', $this->container['locale']));
        }

        if (!in_array('W', explode(', ', $node['access']))) {
            throw new AccessDeniedHttpException($this->container['translator']->trans('node.not_writable', [], 'messages', $this->container['locale']));
        }

        return new JsonResponse($this->container['repository.key']->create($data, $this->container['user']), 201);
    }

    public function update(Request $request, $id)
    {
        $data = $this->handleRequest($request);
        $key = $this->container['repository.key']->findById($id, $this->container['user']);

        if (!$key) {
            throw new NotFoundHttpException($this->container['translator']->trans('key.not_found', [], 'messages', $this->container['locale']));
        }

        $node = $this->container['repository.node']->findById($key['folder'], $this->container['user']);
        if (!in_array('W', explode(', ', $node['access']))) {
            throw new AccessDeniedHttpException($this->container['translator']->trans('node.not_writable', [], 'messages', $this->container['locale']));
        }

        return new JsonResponse($this->container['repository.key']->update($id, $data, $this->container['user']));
    }

    public function delete($id)
    {
        $key = $this->container['repository.key']->findById($id, $this->container['user']);
        if (!$key) {
            throw new NotFoundHttpException($this->container['translator']->trans('key.not_found', [], 'messages', $this->container['locale']));
        }

        $node = $this->container['repository.node']->findById($key['folder'], $this->container['user']);
        if (!in_array('W', explode(', ', $node['access']))) {
            throw new AccessDeniedHttpException($this->container['translator']->trans('node.not_writable', [], 'messages', $this->container['locale']));
        }

        $this->container['repository.key']->delete($id);

        return new JsonResponse(null, 204);
    }

    public function handleRequest(Request $request)
    {
        $data = json_decode($request->getContent(), true);
        if (null === $data) {
            throw new InvalidParameterException($this->container['translator']->trans('invalid_body', [], 'messages', $this->container['locale']));
        }

        if (array_key_exists('id', $data)) {
            unset($data['id']);
        }

        if (array_key_exists('title', $data)) {
            $data['label'] = $data['title'];
            unset($data['title']);
        }

        if (array_key_exists('folder', $data)) {
            $data['id_tree'] = $data['folder'];
            unset($data['folder']);
        }

        if (array_key_exists('username', $data)) {
            $data['login'] = $this->container['api.encoder']->decrypt($data['username']);
            unset($data['username']);
        }

        if (array_key_exists('password', $data)) {
            $data['pw'] = $this->container['api.encoder']->decrypt($data['password']);
            unset($data['password']);
        }

        if (array_key_exists('type', $data)) {
            unset($data['type']);
        }

        return $data;
    }
}
