<?php

class forum extends Controller
{
    public function __construct()
    {
        parent::__construct('main', true, false, true);
        if (!$this->db->forum) {
            NXS::showError();
        }
    }

    public function index(): void
    {
        $this->discussions();
    }

    /*function upload($filename){
        require('upload.php');

        $upload = new Upload();
        call_user_func_array(
            [
                $upload,
                $filename
            ]
        );
    }*/

    public function change_discussion_category(
        $discussionID,
        $categoryID
    ): void {
        $this->checkCSRFToken();

        if (!$this->User->IsMod) {
            exit;
        }

        $forumModel = $this->loadModel('Forum');

        $forumModel->changeDiscussionCategory($discussionID, $categoryID);

        header('Location: ' . URL . 'discussion/' . $discussionID . '/');
        exit();
    }

    public function toggle_discussion_sink($discussionID): void
    {
        $this->checkCSRFToken();

        if (!$this->User->IsMod) {
            exit;
        }

        $adminModel = $this->loadModel('Admin');
        $forumModel = $this->loadModel('Forum');

        if ($adminModel->toggleDiscussionSinked($discussionID)) {
            $forumModel->updateDiscussionForumItem($discussionID);
        }

        header('Location: ' . URL . 'discussion/' . $discussionID . '/');
        exit();
    }

    public function blogs(
        $sort = SORT_BY_BLOG_POSTS,
        $page = 1
    ) {
        switch ($sort) {
            case 'id_desc':
            case 'id_asc':
                break;
            default:
                $sort = SORT_BY_BLOG_POSTS;
        }

        $forumModel = $this->loadModel('Forum');

        if ($blogs = $this->view->blogs = $forumModel->fetchBlog(false, $sort, $page)) {
            $this->view->sortMode = $sort;
            $this->view->pageNumber = htmlspecialchars($page);

            [$this->view->discussionCategories, $namedCategories, $this->view->totalDiscussionCount] = $forumModel->fetchDiscussionCategories();
            $this->view->latestUpdates = $forumModel->fetchLatestUpdates();
            $this->view->userBlog = $forumModel->getUserBlog();
            $this->view->blogPostingPrivileges = $forumModel->fetchBlogPostingPrivileges();
            $this->view->hasReviewableListings = $forumModel->hasReviewableListing();

            $discussionID = $this->view->categoryID = $this->view->categoryAlias = false;

            return $this->view->render('forum/blogs');
        }
    }

    public function search(): void
    {
        $args = func_get_args();

        if (isset($_POST['q'])) {
            $_SESSION['search']['q'] = htmlspecialchars($_POST['q']);
        }

        $this->discussions(
            $args[0] ?? false,
            $args[1] ?? 'recency',
            $args[2] ?? 1,
            $_SESSION['search']['q']
        );
    }

    public function discussions(
        $category = false,
        $sort = 'recency',
        $page = 1,
        $query = false
    ) {
        // Preliminary Validation
        $page = (!is_numeric($page) || $page < 1) ? 1 : $page;

        switch ($sort) {
            case 'comments_desc':
                break;
            default:
                $sort = 'recency';
        }

        $forumModel = $this->loadModel('Forum');

        [$this->view->discussionCategories, $namedCategories, $this->view->totalDiscussionCount] = $forumModel->fetchDiscussionCategories();
        $this->view->latestUpdates = $forumModel->fetchLatestUpdates();
        $this->view->userBlog = $forumModel->getUserBlog();
        $this->view->blogPostingPrivileges = $forumModel->fetchBlogPostingPrivileges();
        $this->view->hasReviewableListings = $forumModel->hasReviewableListing();

        $this->view->searchQuery = $query;

        $category =
            $category == 'all'
            || $category == 'index'
            || (
                !is_numeric($category)
                && !isset($namedCategories[$category])
            )
                ? false
                : (
                    is_numeric($category)
                            ? $category
                            : $namedCategories[$category]
                );

        if (
            [
                $this->view->discussionCount,
                $this->view->discussions
            ] = $forumModel->fetchForumEntries(
                $category,
                $sort,
                $page,
                DISCUSSIONS_PER_PAGE,
                $query
            )
        ) {
            $this->view->categoryID = $category;
            $this->view->categoryAlias = $category ? $this->view->discussionCategories[$category]['alias'] : false;

            if ($category) {
                $this->view->SiteName = $this->view->discussionCategories[$category]['name'] . ': ' . $this->view->SiteName;
            }

            $this->view->pageNumber = htmlspecialchars($page);
            $this->view->sortMode = $sort;
        }

        return $this->view->render('forum/discussions');
    }

    public function mark_all_posts_read(): void
    {
        $this->checkCSRFToken();

        $forumModel = $this->loadModel('Forum');

        $forumModel->markAllPostsRead();

        header('Location: ' . URL . 'discussions/');
        exit;
    }

    public function update_user_flair(
        $userAlias,
        $classID = USER_CLASS_ID_STAR_BUYERS
    ): void {
        $this->checkCSRFToken();

        $forumModel = $this->loadModel('Forum');

        if (!empty($_POST['flair'])) {
            $_POST['flair'] = htmlspecialchars($_POST['flair']);
            $forumModel->editUserFlair(
                $userAlias,
                $classID,
                $_POST['flair']
            );
        }

        if (
            !empty($_POST['edit_flair_return'])
            && preg_match(
                REGEX_URL_SAFE,
                $_POST['edit_flair_return']
            )
        ) {
            header('Location: ' . URL . $_POST['edit_flair_return']);
        } else {
            header('Location: ' . URL . 'discussions/');
        }

        exit;
    }

    public function update_discussion_comment_pictures($discussionCommentID): void
    {
        $this->checkCSRFToken();

        $accountModel = $this->loadModel('Account');

        if (
            $mayUploadPicture =
                $this->User->IsMod
                || $isStarBuyer = $this->User->ascertainUserClass(
                    USER_CLASS_ID_STAR_BUYERS,
                    1,
                    $userRank
                )
        ) {
            if (isset($_POST['delete_pic'])) {
                $accountModel->deleteDiscussionCommentImage(
                    $discussionCommentID,
                    $_POST['delete_pic']
                );
            } elseif (
                isset($_FILES['file'])
                && $accountModel->checkDiscussionCommentImageLimit($discussionCommentID)
            ) {
                if ($isStarBuyer) {
                    $m = new Memcached();
                    $m->addServer('localhost', 11211);
                    $m->setOption(Memcached::OPT_BINARY_PROTOCOL, true);
                    $mKey = 'recentAction-' . $this->User->ID . '-uploadedDiscussionCommentPicture';

                    if (
                        $metDailyLimit =
                            $m->get($mKey) >= FORUM_MAX_UPLOAD_PER_DAY_BY_RANK[$userRank]
                    ) {
                        $_SESSION['too_many_uploads'] = true;

                        header('Location: ' . URL . 'comment/' . $discussionCommentID . '/');
                        exit();
                    }
                }

                $file = $accountModel->uploadFile(
                    'file',
                    true,
                    true,
                    false,
                    false,
                    [
                        [
                            'width' => AVATAR_IMAGE_THUMBNAIL_WIDTH,
                            'height' => AVATAR_IMAGE_THUMBNAIL_HEIGHT,
                            'suffix' => IMAGE_THUMBNAIL_SUFFIX,
                        ],
                    ]
                );

                $imageURL = $file['filepath'] . $file['filename'];

                $validUpload =
                    false !== $file
                    && empty($file['error'])
                    && $imageURL !== 'SS';

                if ($validUpload) {
                    $accountModel->addDiscussionCommentImage(
                        $discussionCommentID,
                        $file['imageID']
                    );
                    if ($isStarBuyer) {
                        $m->increment(
                            $mKey,
                            1,
                            1,
                            USER_CLASS_PRIVILEGES_UPLOADS_INTERVAL
                        );
                    }
                }
            }
        }

        header('Location: ' . URL . 'comment/' . $discussionCommentID . '/');
        exit();
    }

    public function discussion(
        $id,
        $sort = false,
        $page = 1,
        $comment_id = false,
        $return = false,
        $forumModel = false
    ) {
        $forumModel = $forumModel ?: $this->loadModel('Forum');

        $returnSuffix = '';
        if ($return) {
            $returnArray = explode('-', $return);

            if (
                in_array(
                    $returnArray[0],
                    [
                        'id_desc',
                        'id_asc',
                    ]
                )
                && is_numeric($returnArray[1])
            ) {
                $returnSuffix = $returnArray[0] . '/' . $returnArray[1] . '/';
            }
        }

        switch ($sort) {
            case 'comment':
                switch ($page) {
                    case 'reply':
                        $targetAlias = $comment_id;
                        $_SESSION['comment_post']['content'] = '@' . strip_tags($targetAlias) . ' ';

                        header('Location: ' . URL . 'discussion/' . $id . '/' . $returnSuffix);
                        exit();

                        break;
                    case 'quote':
                        if ($quoted_comment = $forumModel->fetchComment($comment_id)) {
                            $_SESSION['comment_post']['content'] = '[quote="' . $comment_id . '"]' . PHP_EOL . preg_replace('/\[quote[^]]*\].*\[\/quote]/is', '', $quoted_comment['content']) . PHP_EOL . '[/quote]';
                        }

                        header('Location: ' . URL . 'discussion/' . $id . '/' . $returnSuffix);
                        exit();

                        break;
                    default:
                        if ($new_comment_id = $forumModel->insertComment($id, $comment_id)) {
                            $forumModel->insertSubscription($id);

                            return $this->comment($new_comment_id, false, $forumModel);
                        }
                        if ($comment_id) {
                            $_SESSION['comment_post']['edit-content'][$comment_id] = $_SESSION['comment_post']['content'];
                            $_SESSION['comment_feedback']['edit-content'][$comment_id] = $_SESSION['comment_feedback']['content'];
                            unset($_SESSION['comment_post']['content'], $_SESSION['comment_feedback']['content']);

                            header('Location: ' . URL . 'discussion/' . $id . '/#edit-' . $comment_id);
                            exit();
                        }
                        header('Location: ' . URL . 'discussion/' . $id . '/');
                        exit();

                }

                break;
            case 'id_desc':
            case 'id_asc':
                break;
            default:
                $sort = false;
        }

        // Preliminary Validation
        $page = (!is_numeric($page) || $page < 1) ? 1 : $page;

        if ([$this->view->commentCount, $this->view->discussion] = $forumModel->fetchComments($id, $sort, $page)) {
            [$this->view->discussionCategories, $namedCategories, $this->view->totalDiscussionCount] = $forumModel->fetchDiscussionCategories();
            $this->view->latestUpdates = $forumModel->fetchLatestUpdates();
            $this->view->userBlog = $forumModel->getUserBlog();
            $this->view->blogPostingPrivileges = $forumModel->fetchBlogPostingPrivileges();
            $this->view->hasReviewableListings = $forumModel->hasReviewableListing();
            $this->view->mayUploadPictures =
                $this->User->IsMod
                || (
                    $this->view->discussion['allowImages']
                    && $this->User->ascertainUserClass(
                        USER_CLASS_ID_STAR_BUYERS,
                        1
                    )
                );

            $this->view->discussionID = htmlspecialchars($id);
            $this->view->categoryID = $this->view->discussion['categoryID'];
            $this->view->categoryAlias = $this->view->discussion['categoryID'] ? $this->view->discussionCategories[$this->view->discussion['categoryID']]['alias'] : false;
            $this->view->pageNumber = $page;
            $this->view->sortMode = !$sort && $this->view->discussion['userID']
                ? 'id_desc'
                : $sort ?: 'id_asc';

            $this->view->forumFilter = !empty($_SESSION['forum'])
                ? $_SESSION['forum']
                : $this->User->Attributes['Preferences']['ForumFilter'];

            $this->view->SiteName = $this->view->discussion['title'] . ': ' . $this->view->SiteName;

            $this->view->render('forum/discussion');
        } else {
            if ($page > 1) {
                header('Location: ' . URL . 'discussion/' . $id . '/');
                exit;
            }
            $_SESSION['temp_notifications'][] = [
                'Content' => 'Discussion does not exist or has been deleted',
                'Anchor' => false,
                'Dismiss' => '.',
                'Design' => [
                    'Color' => 'yellow',
                    'Icon' => Icon::getClass('EXCLAMATION_MARK_IN_CIRCLE'),
                ],
            ];

            header('Location: ' . URL . 'discussions/');
            exit;

        }
    }

    public function blog($alias, $sort = SORT_BY_BLOG_POSTS, $page = 1)
    {
        $forumModel = $this->loadModel('Forum');

        if ($blog = $this->view->blog = $forumModel->fetchBlog($alias, $sort, $page)) {
            $this->view->sortMode = htmlspecialchars($sort);
            $this->view->pageNumber = htmlspecialchars($page);
            $this->view->blogAlias = $blog['Alias'];

            [$this->view->discussionCategories, $namedCategories, $this->view->totalDiscussionCount] = $forumModel->fetchDiscussionCategories();
            $this->view->latestUpdates = $forumModel->fetchLatestUpdates();
            $this->view->userBlog = $forumModel->getUserBlog();
            $this->view->blogPostingPrivileges = $forumModel->fetchBlogPostingPrivileges();
            $this->view->hasReviewableListings = $forumModel->hasReviewableListing();

            $discussionID = $blog['DiscussionCategoryID'];
            $this->view->categoryID = $discussionID;
            $this->view->categoryAlias = $discussionID
                ? $this->view->discussionCategories[$discussionID]['alias']
                : false;

            $this->view->SiteName = $blog['Title'] . ': ' . $this->view->SiteName;

            // print_r($this->view->blog);
            // die;

            return $this->view->render('forum/blog');
        }
        header('Location: ' . URL . 'discussions/');
        exit;

    }

    public function post($ID, $commentSort = SORT_BY_BLOG_POST_COMMENTS, $commentPage = 1)
    {
        $forumModel = $this->loadModel('Forum');

        if (
            $blogPost = $this->view->blogPost = $forumModel->fetchBlogPost(
                $ID,
                $commentSort,
                $commentPage
            )
        ) {
            [$this->view->discussionCategories, $namedCategories, $this->view->totalDiscussionCount] = $forumModel->fetchDiscussionCategories();
            $this->view->latestUpdates = $forumModel->fetchLatestUpdates();
            $this->view->userBlog = $forumModel->getUserBlog();

            $this->view->categoryID = $blogPost['DiscussionCategoryID'];
            $this->view->categoryAlias = $blogPost['DiscussionCategoryID']
                ? $this->view->discussionCategories[$blogPost['DiscussionCategoryID']]['alias']
                : false;

            $this->view->blogPostingPrivileges = $forumModel->fetchBlogPostingPrivileges();
            $this->view->blogAlias = $blogPost['BlogAlias'];
            $this->view->hasReviewableListings = $forumModel->hasReviewableListing();

            $this->view->postID = htmlspecialchars($ID);
            $this->view->sortMode = htmlspecialchars($commentSort);
            $this->view->pageNumber = htmlspecialchars($commentPage);

            $this->view->SiteName =
                (
                    $blogPost['Title']
                        ? $blogPost['Title'] . ': '
                        : false
                ) . $blogPost['BlogTitle'] . '. ' . $this->view->SiteName;

            return $this->view->render('forum/post');
        }
        header('Location: ' . URL . 'discussions/');
        exit;

    }

    public function comment_blog_post($blogPostID): void
    {
        $_SESSION['blog_post_comment_post']['content'] = false;

        header('Location: ' . URL . 'post/' . $blogPostID . '/');
        exit;
    }

    public function reply_blog_post_comment($blogPostCommentID): void
    {
        $forumModel = $this->loadModel('Forum');

        if (
            $blogPostCommenterAlias = $forumModel->findBlogPostCommenterAlias($blogPostCommentID)
        ) {
            $_SESSION['blog_post_comment_post']['content'] = '@' . $blogPostCommenterAlias . ' ';

            $this->blog_post_comment($blogPostCommentID, $forumModel, false);
        } else {
            header('Location: ' . URL . 'discussions/');
            exit;
        }
    }

    public function quote_blog_post_comment($blogPostCommentID): void
    {
        $forumModel = $this->loadModel('Forum');

        if (
            $blogPostCommentContent = $forumModel->getBlogPostCommentContent($blogPostCommentID)
        ) {
            $_SESSION['blog_post_comment_post']['content'] = '[quote]' . trim(preg_replace('/\s+/', ' ', preg_replace('/\[quote\](?:(?!\[\/quote).)*\[\/quote]/is', '', $blogPostCommentContent))) . '[/quote]' . PHP_EOL . PHP_EOL;

            $this->blog_post_comment($blogPostCommentID, $forumModel, false);
        } else {
            header('Location: ' . URL . 'discussions/');
            exit;
        }
    }

    public function blog_post_comment($blogPostCommentID, $forumModel = false, $anchor = true): void
    {
        $forumModel = $forumModel ?: $this->loadModel('Forum');

        if (
            [
                $blogPostID,
                $blogPostPage
            ] = $forumModel->findBlogPostComment($blogPostCommentID)
        ) {
            header('Location: ' . URL . 'post/' . $blogPostID . '/' . SORT_BY_BLOG_POST_COMMENTS . '/' . $blogPostPage . '/' . ($anchor ? '#comment-' . $blogPostCommentID : false));
            exit;
        }
        header('Location: ' . URL . 'discussions/');
        exit;

    }

    public function post_blog_post_comment($blogPostID)
    {
        $forumModel = $this->loadModel('Forum');

        if ($newCommentID = $forumModel->insertBlogPostComment($blogPostID, $blogID)) {
            $forumModel->insertBlogPostSubscription($blogPostID, $newCommentID);
            // $forumModel->insertBlogSubscription($blogID);

            unset(
                $_SESSION['blog_post_comment_post'],
                $_SESSION['blog_post_comment_feedback']
            );

            return $this->blog_post_comment($newCommentID, $forumModel);
        }
        header('Location: ' . URL . 'post/' . $blogPostID . '/');
        exit();

    }

    public function edit_blog_post($blogPostID): void
    {
        $forumModel = $this->loadModel('Forum');

        if (
            $forumModel->editBlogPost($blogPostID)
        ) {
            header('Location: ' . URL . 'post/' . $blogPostID . '/');
            exit();
        }

        header('Location: ' . URL . 'post/' . $blogPostID . '/#edit-' . $blogPostID);
        exit();
    }

    public function edit_blog_post_comment($blogPostCommentID)
    {
        $forumModel = $this->loadModel('Forum');

        if (
            [
                $blogPostID,
                $blogPostPage
            ] = $forumModel->findBlogPostComment($blogPostCommentID)
        ) {
            if ($forumModel->editBlogPostComment($blogPostCommentID)) {
                return $this->blog_post_comment($blogPostCommentID, $forumModel);
            }

            header('Location: ' . URL . 'post/' . $blogPostID . '/' . SORT_BY_BLOG_POST_COMMENTS . '/' . $blogPostPage . '/#edit-' . $blogPostCommentID);
            exit();
        }
        header('Location: ' . URL . '/');
        exit();
    }

    public function comment($id, $sort = false, $forumModel = false): void
    {
        $forumModel = $forumModel ?: $this->loadModel('Forum');
        $sort = $sort ?: 'id_asc';

        if ([$discussion_id, $page] = $forumModel->findComment($id)) {
            header('Location: ' . URL . 'discussion/' . $discussion_id . '/' . $sort . '/' . $page . '/#comment-' . $id);
            exit();
        }
        $_SESSION['temp_notifications'][] = [
            'Content' => 'Comment does not exist or has been deleted',
            'Anchor' => false,
            'Dismiss' => '.',
            'Design' => [
                'Color' => 'yellow',
                'Icon' => Icon::getClass('EXCLAMATION_MARK_IN_CIRCLE'),
            ],
        ];

        header('Location: ' . URL . 'discussions/');
        exit;

    }

    public function delete_comment($comment_id, $forumModel = false): void
    {
        $this->checkCSRFToken();

        $forumModel = $forumModel ?: $this->loadModel('Forum');

        if ([$discussion_id, $page] = $forumModel->findComment($comment_id)) {
            if ($forumModel->deleteDiscussionComment($comment_id)) {
                $_SESSION['temp_notifications'][] = [
                    'Content' => 'Comment deleted successfully',
                    'Anchor' => false,
                    'Dismiss' => '.',
                    'Design' => [
                        'Color' => 'green',
                        'Icon' => Icon::getClass('CHECK'),
                    ],
                ];
            } else {
                $_SESSION['temp_notifications'][] = [
                    'Content' => 'Could not be deleted',
                    'Anchor' => false,
                    'Dismiss' => '.',
                    'Design' => [
                        'Color' => 'yellow',
                        'Icon' => Icon::getClass('EXCLAMATION_MARK_IN_CIRCLE'),
                    ],
                ];
            }

            header('Location: ' . URL . 'discussion/' . $discussion_id . '/id_asc/' . $page . '/');
            exit();
        }
        $_SESSION['temp_notifications'][] = [
            'Content' => 'Comment does not exist or has been deleted',
            'Anchor' => false,
            'Dismiss' => '.',
            'Design' => [
                'Color' => 'yellow',
                'Icon' => Icon::getClass('EXCLAMATION_MARK_IN_CIRCLE'),
            ],
        ];

        header('Location: ' . URL . 'discussions/');
        exit;

    }

    public function clear_discussion_comment_reports($commentID)
    {
        $this->checkCSRFToken();

        if (!$this->User->IsMod) {
            exit;
        }

        $forumModel = $this->loadModel('Forum');

        if ([$discussionID, $page] = $forumModel->findComment($commentID)) {
            if (!$forumModel->clearDiscussionCommentReports($commentID)) {
                $_SESSION['temp_notifications'][] = [
                    'Content' => 'Could not report comment',
                    'Anchor' => false,
                    'Dismiss' => '.',
                    'Design' => [
                        'Color' => 'yellow',
                        'Icon' => Icon::getClass('EXCLAMATION_MARK_IN_CIRCLE'),
                    ],
                ];
            }

            return $this->comment($commentID, false, $forumModel);
        }
        $_SESSION['temp_notifications'][] = [
            'Content' => 'Comment does not exist or has been deleted',
            'Anchor' => false,
            'Dismiss' => '.',
            'Design' => [
                'Color' => 'yellow',
                'Icon' => Icon::getClass('EXCLAMATION_MARK_IN_CIRCLE'),
            ],
        ];

        header('Location: ' . URL . 'discussions/');
        exit;

    }

    public function report_comment($commentID, $forumModel = false)
    {
        $this->checkCSRFToken();

        $forumModel = $forumModel ?: $this->loadModel('Forum');

        if ([$discussionID, $page] = $forumModel->findComment($commentID)) {
            if (!$forumModel->toggleCommentReport($commentID)) {
                $_SESSION['temp_notifications'][] = [
                    'Content' => 'Could not report comment',
                    'Anchor' => false,
                    'Dismiss' => '.',
                    'Design' => [
                        'Color' => 'yellow',
                        'Icon' => Icon::getClass('EXCLAMATION_MARK_IN_CIRCLE'),
                    ],
                ];
            }

            return $this->comment($commentID, false, $forumModel);
        }
        $_SESSION['temp_notifications'][] = [
            'Content' => 'Comment does not exist or has been deleted',
            'Anchor' => false,
            'Dismiss' => '.',
            'Design' => [
                'Color' => 'yellow',
                'Icon' => Icon::getClass('EXCLAMATION_MARK_IN_CIRCLE'),
            ],
        ];

        header('Location: ' . URL . 'discussions/');
        exit;

    }

    public function create($category = false, $forumModel = false): void
    {
        $forumModel = $forumModel ?: $this->loadModel('Forum');

        [$this->view->discussionCategories, $namedCategories, $this->view->totalDiscussionCount] = $forumModel->fetchDiscussionCategories();
        $this->view->latestUpdates = $forumModel->fetchLatestUpdates();
        $this->view->userBlog = $forumModel->getUserBlog();
        $this->view->blogPostingPrivileges = $forumModel->fetchBlogPostingPrivileges();
        $this->view->hasReviewableListings = $forumModel->hasReviewableListing();

        $this->view->category = htmlspecialchars($category);
        switch (true) {
            case $category == 'review'
            && $this->view->hasReviewableListings:
                $this->view->reviewableListings = $forumModel->getReviewableListings();

                break;
            default:
                $this->view->categoryID = $category ? $namedCategories[$category] : false;
                $this->view->categoryAlias = $category ? $this->view->discussionCategories[$this->view->categoryID]['alias'] : false;
        }

        $this->view->render('forum/create');
    }

    public function create_post($blogAlias = false)
    {
        $forumModel = $this->loadModel('Forum');

        [
            $blogCategories,
            $myBlog
        ] = $forumModel->fetchBlogsWithPostingPrivileges($blogAlias);

        if ($blogCategories) {
            $this->view->blogCategories = $blogCategories;

            [$this->view->discussionCategories, $namedCategories, $this->view->totalDiscussionCount] = $forumModel->fetchDiscussionCategories();
            $this->view->latestUpdates = $forumModel->fetchLatestUpdates();
            $this->view->userBlog = $forumModel->getUserBlog();
            $this->view->blogPostingPrivileges = $forumModel->fetchBlogPostingPrivileges();
            $this->view->hasReviewableListings = $forumModel->hasReviewableListing();

            if ($myBlog) {
                $this->view->blogAlias = htmlspecialchars($blogAlias);

                $categoryID = $this->view->categoryID = $myBlog['CategoryID'];
                $this->view->categoryAlias = $myBlog['CategoryAlias'];
            }

            return $this->view->render('forum/create_blog_post');
        }

        header('Location: ' . URL . 'discussions/');
        exit;
    }

    public function create_blog_post($forumModel = false): void
    {
        $forumModel = $forumModel ?: $this->loadModel('Forum');

        if ($blogPostID = $forumModel->insertBlogPost()) {
            $forumModel->insertBlogPostSubscription($blogPostID);

            header('Location: ' . URL . 'post/' . $blogPostID . '/');
            exit();
        }

        header('Location: ' . URL . 'discussions/');
        exit;
    }

    public function create_discussion($forumModel = false): void
    {
        $forumModel = $forumModel ?: $this->loadModel('Forum');

        if ($discussion_id = $forumModel->insertDiscussion()) {
            $forumModel->insertSubscription($discussion_id);

            header('Location: ' . URL . 'discussion/' . $discussion_id . '/');
            exit();
        }
        header('Location: ' . URL . 'forum/create/' . (isset($_POST['listing']) ? 'review/' : false));
        exit;

    }

    public function create_vendor_discussion($forumModel = false): void
    {
        $forumModel = $forumModel ?: $this->loadModel('Forum');

        if ($discussionID = $forumModel->insertUserDiscussion()) {
            $forumModel->insertSubscription($discussionID);

            header('Location: ' . URL . 'discussion/' . $discussionID . '/');
            exit();
        }
        header('Location: ' . URL . '#vendor-thread');
        exit;

    }

    public function delete_discussion($discussionID, $forumModel = false): void
    {
        $forumModel = $forumModel ?: $this->loadModel('Forum');

        if ($forumModel->deleteDiscussion($discussionID)) {
            $_SESSION['temp_notifications'][] = [
                'Content' => 'Discussion deleted successfully',
                'Anchor' => false,
                'Dismiss' => '.',
                'Design' => [
                    'Color' => 'green',
                    'Icon' => Icon::getClass('CHECK'),
                ],
            ];
        }

        header('Location: ' . URL . 'discussions/');
        exit;
    }

    public function delete_blog_post($blogPostID, $forumModel = false): void
    {
        $forumModel = $forumModel ?: $this->loadModel('Forum');

        if ($forumModel->deleteBlogPost($blogPostID)) {
            $_SESSION['temp_notifications'][] = [
                'Content' => 'Blog post deleted successfully',
                'Anchor' => false,
                'Dismiss' => '.',
                'Design' => [
                    'Color' => 'green',
                    'Icon' => Icon::getClass('CHECK'),
                ],
            ];
        }

        header('Location: ' . URL . 'discussions/');
        exit;
    }

    public function subscribe($discussion_id, $page = 1, $forumModel = false): void
    {
        $forumModel = $forumModel ?: $this->loadModel('Forum');

        if ($forumModel->insertSubscription($discussion_id)) {
            header('Location: ' . URL . 'discussion/' . $discussion_id . '/id_asc/' . $page . '/');
            exit();
        }
        $_SESSION['temp_notifications'][] = [
            'Content' => 'Could not subscribe to this discussion',
            'Anchor' => false,
            'Dismiss' => '.',
            'Design' => [
                'Color' => 'yellow',
                'Icon' => Icon::getClass('EXCLAMATION_MARK_IN_CIRCLE'),
            ],
        ];

        header('Location: ' . URL . 'discussions/');
        exit;

    }

    public function unsubscribe($discussion_id, $page = 1, $forumModel = false): void
    {
        $forumModel = $forumModel ?: $this->loadModel('Forum');

        if ($forumModel->deleteSubscription($discussion_id)) {
            header('Location: ' . URL . 'discussion/' . $discussion_id . '/id_asc/' . $page . '/');
            exit();
        }
        $_SESSION['temp_notifications'][] = [
            'Content' => 'Could not remove subcription to this discussion',
            'Anchor' => false,
            'Dismiss' => '.',
            'Design' => [
                'Color' => 'yellow',
                'Icon' => Icon::getClass('EXCLAMATION_MARK_IN_CIRCLE'),
            ],
        ];

        header('Location: ' . URL . 'discussions/');
        exit;

    }

    public function toggle_subscription($type, $ID, $sortMode = SORT_BY_BLOG_POSTS, $page = 1): void
    {
        $forumModel = $this->loadModel('Forum');

        switch ($type) {
            case 'blog':
                if ($forumModel->toggleBlogSubscription($ID)) {
                    $blogAlias = $forumModel->fetchBlogAlias($ID);

                    header('Location: ' . URL . 'blog/' . $blogAlias . '/' . $sortMode . '/' . $page . '/');
                    exit;
                }

                break;
            case 'blog_post':
                if ($forumModel->toggleBlogPostSubscription($ID)) {
                    header('Location: ' . URL . 'post/' . $ID . '/' . $sortMode . '/' . $page . '/');
                    exit;
                }

                break;
        }

        header('Location: ' . URL . 'discussions/');
        exit;
    }

    public function vote($comment_id, $direction, $forumModel = false)
    {
        $forumModel = $forumModel ?: $this->loadModel('Forum');
        $direction = 'up';
        switch ($direction) {
            case 'up':
                $vote = 1;

                break;
            case 'down':
                $vote = -1;

                break;
        }

        if ([$discussion_id, $page] = $forumModel->findComment($comment_id)) {
            if (!$forumModel->addVote($comment_id, $vote)) {
                $_SESSION['temp_notifications'][] = [
                    'Content' => 'Could not vote',
                    'Anchor' => false,
                    'Dismiss' => '.',
                    'Design' => [
                        'Color' => 'yellow',
                        'Icon' => Icon::getClass('EXCLAMATION_MARK_IN_CIRCLE'),
                    ],
                ];
            }

            return $this->comment($comment_id, false, $forumModel);
            // header('Location: ' . URL . 'comment/' . $comment_id . '/');
            // die();
        }
        $_SESSION['temp_notifications'][] = [
            'Content' => 'Comment does not exist or has been deleted',
            'Anchor' => false,
            'Dismiss' => '.',
            'Design' => [
                'Color' => 'yellow',
                'Icon' => Icon::getClass('EXCLAMATION_MARK_IN_CIRCLE'),
            ],
        ];

        header('Location: ' . URL . 'discussions/');
        exit;

    }

    public function unvote($comment_id, $forumModel = false)
    {
        $forumModel = $forumModel ?: $this->loadModel('Forum');

        if ([$discussion_id, $page] = $forumModel->findComment($comment_id)) {
            if (!$forumModel->deleteVote($comment_id)) {
                $_SESSION['temp_notifications'][] = [
                    'Content' => 'Could not unvote',
                    'Anchor' => false,
                    'Dismiss' => '.',
                    'Design' => [
                        'Color' => 'yellow',
                        'Icon' => Icon::getClass('EXCLAMATION_MARK_IN_CIRCLE'),
                    ],
                ];
            }

            // header('Location: ' . URL . 'discussion/' . $discussion_id . '/id_asc/' . $page . '/#comment-' . $comment_id);
            // die();
            return $this->comment($comment_id, false, $forumModel);
        }
        $_SESSION['temp_notifications'][] = [
            'Content' => 'Comment does not exist or has been deleted',
            'Anchor' => false,
            'Dismiss' => '.',
            'Design' => [
                'Color' => 'yellow',
                'Icon' => Icon::getClass('EXCLAMATION_MARK_IN_CIRCLE'),
            ],
        ];

        header('Location: ' . URL . 'discussions/');
        exit;

    }
}
