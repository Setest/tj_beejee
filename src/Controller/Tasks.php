<?php

namespace App\Controller;

use App\Model\Task;
use Pagerfanta\Adapter\NullAdapter;
use Pagerfanta\Pagerfanta;
use Pagerfanta\View\TwitterBootstrap5View;

class Tasks extends AbstractController
{
    const LIMIT = 3;

    public function list(int $page)
    {
        $offset = $this->getOffsetByPage($page);
        $result = Task::findAll(self::LIMIT, $offset);
        
        $total = Task::count();
        $pagination = $this->pagination($page,  $total, self::LIMIT, '/tasks/%d');

        return $this->renderAndReturnResponse('tasks.html.twig', [
            'tasks' => $result,
            'pagination' => $pagination,
        ]);
    }
    
    /**
     * Generate rendered pagination
     *
     * @param int    $page
     * @param int    $total
     * @param int    $limit
     * @param string $link
     *
     * @return string
     */
    public function pagination(int $page, int $total, int $limit, string $link): string {
        $adapter    = new NullAdapter($total);
        $pagerfanta = new Pagerfanta($adapter);
        
        $pagerfanta->setMaxPerPage($limit)->setCurrentPage($page);
        
        $view    = new TwitterBootstrap5View();
        $options = [
            'css_container_class' => 'pagination justify-content-center',
        ];
        
        return $view->render($pagerfanta, fn($pageNum) => sprintf($link, $pageNum), $options);
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