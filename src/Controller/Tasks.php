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

        return $this->renderAndReturnResponse('tasks.html.twig', [
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
}