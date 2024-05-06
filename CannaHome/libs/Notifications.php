<?php

class Notifications
{
    public $all = [];

    public function custom(
        $content,
        $anchor = false,
        $dismiss = false,
        $group = 'General',
        $design = [
            'Color' => 'blue',
            'Icon' => 'default',
        ],
        $target = '_self',
        $sound = false
    ): void {
        $design = array_merge(['Color' => 'blue', 'Icon' => 'default'], $design);
        $this->all[$group][] = [
            'Content' => $content,
            'Anchor' => $anchor,
            'Dismiss' => $dismiss,
            'Design' => $design,
            'Target' => $target,
            'ID' => 'notf-' . crc32($group . $content),
            'Sound' => $sound,
        ];
    }

    public function quick($identifier, $content = false, $group = false): void
    {
        switch ($identifier) {
            case 'FatalError':
                $this->custom($content ?: 'Something went horribly wrong!', false, false, $group ?: 'General', [
                    'Color' => 'yellow',
                    'Icon' => Icon::getClass('EXCLAMATION_MARK_IN_TRIANGLE'),
                ]);

                break;
            case 'RequestError':
                $this->custom($content ?: 'Something went horribly wrong!', false, '.', $group ?: 'General', [
                    'Color' => 'yellow',
                    'Icon' => Icon::getClass('EXCLAMATION_MARK_IN_TRIANGLE'),
                ]);

                break;
            case 'Info':
                $this->custom($content, false, false, $group ?: 'General', [
                    'Color' => 'blue',
                    'Icon' => Icon::getClass('EXCLAMATION_MARK_IN_CIRCLE'),
                ]);

                break;
        }
    }

    public function render($groups = false, $format = false, $tags = [])
    {
        if ($groups) {
            $notifications_groups = array_merge(
                array_intersect_key(
                    array_flip($groups),
                    $this->all
                ),
                array_intersect_key(
                    $this->all,
                    array_flip($groups)
                )
            );
        } else {
            $notifications_groups = $this->all;
        }

        if (count($notifications_groups) == 0) {
            return false;
        }

        echo $notifications_groups && isset($tags[0]) ? $tags[0] : false;
        foreach ($notifications_groups as $group => $notifications) {
            foreach ($notifications as $notification) {
                switch ($format) {
                    case 'list':
                        echo '
							<li id="' . $notification['ID'] . '" class="' . $notification['Design']['Color'] . '">
								' . ($notification['Dismiss'] ? '<a class="close" href="' . $notification['Dismiss'] . '">&times;</a>' : false) . '
								<' .
                                (
                                    $notification['Anchor']
                                        ? 'a href="' . $notification['Anchor'] . '"' .
                                            (
                                                isset($notification['Target']) && $notification['Target']
                                                    ? ' target="' . $notification['Target'] . '"'
                                                    : false
                                            )
                                        : 'div'
                                )
                                . '>
									' . ($notification['Design']['Icon'] == 'none' ? '' : '<i class="' . ($notification['Design']['Icon'] == 'default' ? Icon::getClass('EXCLAMATION_MARK_IN_CIRCLE') : $notification['Design']['Icon']) . '"></i>') . '
									<div>
										<div>
											<span>' . ($notification['Content']) . '</span>
										</div>
									</div>
								</' . ($notification['Anchor'] ? 'a' : 'div') . '>
							</li>';

                        break;
                    case 'fixed':
                        echo '
							<div id="' . $notification['ID'] . '" class="important-notification ' . $notification['Design']['Color'] . '">
								' . ($notification['Dismiss'] ? '<a class="close" href="' . $notification['Dismiss'] . '">&times;</a>' : false) . '
								' .
                                (
                                    $notification['Anchor']
                                        ? '<a class="notification-link" href="' . $notification['Anchor'] . '"' .
                                            (
                                                isset($notification['Target']) && $notification['Target']
                                                    ? ' target="' . $notification['Target'] . '"'
                                                    : false
                                            ) . '>'
                                        : false
                                )
                                    . ($notification['Design']['Icon'] == 'none' ? '' : '<i class="' . ($notification['Design']['Icon'] == 'default' ? Icon::getClass('EXCLAMATION_MARK_IN_CIRCLE') : $notification['Design']['Icon']) . '"></i>') . '
									<p>' . ($notification['Content']) . '</p>
								' . ($notification['Anchor'] ? '</a>' : false) . '
							</div>
						';

                        break;
                    default:
                        echo '
							<div id="' . $notification['ID'] . '" class="notification ' . $notification['Design']['Color'] . '">
								<' . ($notification['Anchor'] ? 'a href="' . $notification['Anchor'] . '"' : 'div') . '>
									' . ($notification['Design']['Icon'] == 'none' ? '' : '<i class="' . $notification['Design']['Icon'] . '"></i>') . '
									<div>
										<div><span>' . $notification['Content'] . '</span></div>
									</div>
								</' . ($notification['Anchor'] ? 'a' : 'div') . '>
								' . ($notification['Dismiss'] ? '<a class="close" href="' . $notification['Dismiss'] . '">&times;</a>' : false) . '
							</div>';

                        break;
                }
            }
        }
        echo $notifications_groups && isset($tags[1]) ? $tags[1] : false;

        return true;
    }
}
