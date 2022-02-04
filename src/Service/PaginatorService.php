<?php

namespace App\Service;

use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;

class PaginatorService
{
    public function paginate($outings, PaginatorInterface $paginator, Request $request)
    {
        return $paginator->paginate($outings, $request->query->getInt('page', 1), 15);
    }
}
