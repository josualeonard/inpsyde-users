<?php // phpcs:disable Generic.Files.LineEndings.InvalidEOLChar
    // Set defaults.
    $args = wp_parse_args(
        $args,
        ['data' => []]
    );
    $data = isset($args['data'])?$args['data']:[];
    echo json_encode($data);
