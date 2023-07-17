<?php

namespace App\Controller;

use App\Model\Task;

class Tasks extends AbstractController
{
    const LIMIT = 3;

    public function list(int $page)
    {
        $orderBy = $this->getRequest()->getQueryParams()[self::ORDER_URL_OPTION_NAME] ?? 'id';
        $orderDirection = $this->getRequest()->getQueryParams()[self::DIRECTION_URL_OPTION_NAME] ?? 'DESC';
        
        $orderBy = Task::filterOrderFieldByName($orderBy);
        $orderDirection = Task::filterOrderDirectionByName($orderDirection);
        
        $offset = $this->getOffsetByPage($page);
        $result = Task::findAll(self::LIMIT, $offset, $orderBy, $orderDirection);
        
        $total = Task::count();
        $pagination = $this->pagination($page, $total, self::LIMIT, [
            self::ORDER_URL_OPTION_NAME => $orderBy,
            self::DIRECTION_URL_OPTION_NAME => $orderDirection
        ]);
        
        $template = (\App\Helper\Auth::isUserAuthorized()) ? 'tasks_editor' : 'tasks';

        return $this->renderAndReturnResponse($template . '.html.twig', [
            'tasks' => $result,
            'pagination' => $pagination,
            'sortFields' => Task::getFields(),
            self::ORDER_URL_OPTION_NAME => $orderBy,
            self::DIRECTION_URL_OPTION_NAME => $orderDirection,
        ]);
    }
    
    private function getOffsetByPage(int $page): int
    {
        if ($page <= 1) {
            return 0;
        }

        $total = Task::count();
        if ($total < $page * self::LIMIT && $total > self::LIMIT) {
            return $total - self::LIMIT;
        }

        return $page * self::LIMIT;
    }

    public function create()
    {
        $data = $this->getRequest()->getParsedBody();
        $errors = [];
        
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Email address is considered valid";
        }
        
        if (!$data['username']) {
            $errors[] = "User name is no set";
        }

        if (!$data['content']) {
            $errors[] = "Content is empty";
        }
        
        if ($errors){
            return $this->renderAndReturnResponse('tasks_creating_failed.html.twig', [
                'errors' => $errors
            ], 400);
        }
        
        if (!Task::insert($data['username'], $data['email'], $data['content'])) {
            return $this->renderAndReturnResponse('tasks_creating_failed.html.twig', [
                'errors' => ['Something goes wrong, try again latter']
            ], 500);
        }
        
        return $this->renderAndReturnResponse('tasks_created_successful.html.twig');
    }
    
    public function update(int $id)
    {
        if (!\App\Helper\Auth::isUserAuthorized()){
            return $this->returnJsonResponse(
                [
                    'status'  => false,
                    'message' => 'You have no permissions',
                ],
                500
            );
        }
        
        $data = $this->getRequest()->getBody()->getContents();
        $data = json_decode($data,true);
        
        if (!$data){
            return $this->returnJsonResponse(
                [
                    'status'  => false,
                    'message' => 'Data is empty',
                ],
                500
            );
        }


        $errors = [];
        
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Email address is considered valid";
        }
        
        if (!$data['username']) {
            $errors[] = "User name is no set";
        }
        
        if (!$data['content']) {
            $errors[] = "Content is empty";
        }
        
        if ($errors) {
            return $this->returnJsonResponse(
                [
                    'status'  => false,
                    'message' => implode(', ', $errors),
                ],
                400
            );
        }
        
        $task = Task::findById($id);
        if (!$task){
            return $this->returnJsonResponse(
                [
                    'status'  => false,
                    'message' => sprintf('Task with id: %d is not exist', $id),
                ],
                500
            );
        }
        
        $data['edited_at'] = $task['edited_at'] ?? null;
        if ($this->hasChanges($task, $data)){
            $data['edited_at'] = time();
        }
        
        $data = [...$task, ...$data];
        if (!Task::update($data['id'], $data['username'], $data['email'], $data['content'], !!$data['done'], $data['edited_at'])) {
            return $this->returnJsonResponse(
                [
                    'status'  => false,
                    'message' => 'Something goes wrong, try again latter',
                ],
                500
            );
        }
        
        return $this->returnJsonResponse(
            [
                'status'  => true,
            ],
            200
        );
    }
    
    private function hasChanges($oldData, $newData): bool
    {
        $ff = ['username', 'email', 'content'];
        foreach ($ff as $f) {
            if ($oldData[$f] !== $newData[$f]){
                return true;
            }
        }
        
        return false;
    }
}