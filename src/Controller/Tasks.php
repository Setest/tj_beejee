<?php

namespace App\Controller;

use App\Model\Task;
use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\ResponseInterface;

class Tasks extends AbstractController
{
    const LIMIT = 5;

    public function list(int $page)
    {
        $offset = $this->getOffsetByPage($page);
        $result = Task::findAll(self::LIMIT, $offset);
//        var_dump($result);
        return $this->renderAndReturnResponse('tasks.html.twig', ['tasks'=>$result]);
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
        $request = $this->getRequest();
    }
}