<?php

declare(strict_types=1);

final class AboutController extends Controller
{
    public function index(): void
    {
        $this->render('about', [
            'title'   => 'About',
            'heading' => 'About this stack',
        ]);
    }
}
