<?php

class search extends Controller
{
    public function listings(): void
    {
        $args = func_get_args();

        if (isset($_POST['q'])) {
            $_SESSION['search']['q'] = htmlspecialchars($_POST['q']);
        }

        require 'catalog.php';

        $catalog = new Catalog();

        $catalog->listings(
            $args[0] ?? false,
            false,
            $args[2] ?? false,
            $_SESSION['search']['q']
        );
    }

    public function forum(): void
    {
        $args = func_get_args();

        if (isset($_GET['q'])) {
            $_SESSION['search']['q'] = htmlspecialchars($_POST['q']);
        }

        require 'forum.php';

        $forum = new Forum();

        $forum->discussions(
            $args[0] ?? false,
            $args[1] ?? 'recency',
            $args[2] ?? 1,
            $_SESSION['search']['q']
        );
    }
}
