<?php

namespace Teampass\Api\Controller;

use Silex\ControllerCollection;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Exception\InvalidParameterException;

final class NodeController extends AbstractController
{
    public function all()
    {
        return new JsonResponse($this->container['repository.node']->findAllByUser($this->container['user']), 200);
    }

    public function show($id)
    {
        $node = $this->container['repository.node']->findById($id, $this->container['user']);
        if (false === $node) {
            throw new NotFoundHttpException($this->container['translator']->trans('node.not_found', [], 'messages', $this->container['locale']));
        }

        if (!in_array('W', explode(', ', $node['access'])) && !in_array('R', explode(', ', $node['access']))) {
            throw new AccessDeniedHttpException($this->container['translator']->trans('node.not_read', [], 'messages', $this->container['locale']));
        }

        return new JsonResponse($node, 200);
    }

    public function create(Request $request)
    {
        $data = $this->handleRequest($request);
        if (0 != $data['parent_id']) {
            $node = $this->container['repository.node']->findById($data['parent_id'], $this->container['user']);
            if (false === $node) {
                throw new NotFoundHttpException($this->container['translator']->trans('node.not_found', [], 'messages', $this->container['locale']));
            }

            if (!in_array('W', explode(', ', $node['access']))) {
                throw new AccessDeniedHttpException($this->container['translator']->trans('node.not_writable', [], 'messages', $this->container['locale']));
            }
        }

        $node = $this->container['repository.node']->create($data, $this->container['user']);
        if (false === $node) {
            throw new ConflictHttpException($this->container['translator']->trans('node.already_exists', [], 'messages', $this->container['locale']));
        }

        return new JsonResponse($node, 201);
    }

    public function update(Request $request, $id)
    {
        $data = $this->handleRequest($request);
        $node = $this->container['repository.node']->findById($id, $this->container['user']);

        if (false === $node) {
            throw new NotFoundHttpException($this->container['translator']->trans('node.not_found', [], 'messages', $this->container['locale']));
        }

        if (!in_array('W', explode(', ', $node['access']))) {
            throw new AccessDeniedHttpException($this->container['translator']->trans('node.not_writable', [], 'messages', $this->container['locale']));
        }

        return new JsonResponse($this->container['repository.node']->update($id, $data, $this->container['user']));
    }

    public function delete($id)
    {
        $node = $this->container['repository.node']->findById($id, $this->container['user']);
        if (!$node) {
            throw new NotFoundHttpException($this->container['translator']->trans('node.not_found', [], 'messages', $this->container['locale']));
        }

        if (!in_array('W', explode(', ', $node['access']))) {
            throw new AccessDeniedHttpException($this->container['translator']->trans('node.not_deleted', [], 'messages', $this->container['locale']));
        }

        $this->container['repository.node']->delete($id);

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

        if (array_key_exists('type', $data)) {
            unset($data['type']);
        }

        if (array_key_exists('access', $data)) {
            unset($data['access']);
        }

        if (!array_key_exists('parent_id', $data)) {
            $data['parent_id'] = 0;
        }

        if (!array_key_exists('complication', $data)) {
            $data['complication'] = 0;
        }

        if (!in_array($data['complication'], [0, 25, 50, 60, 70, 80, 90])) {
            throw new ConflictHttpException($this->container['translator']->trans('complication.is_wrong', [], 'messages', $this->container['locale']));
        }

        return $data;
    }

    protected function addRoutes(ControllerCollection $controllers)
    {
        $controllers->get('/nodes', [$this, 'all']);
        $controllers->get('/node/{id}', [$this, 'show']);
        $controllers->post('/node', [$this, 'create']);
        $controllers->put('/node/{id}', [$this, 'update']);
        $controllers->delete('/node/{id}', [$this, 'delete']);
    }
}
