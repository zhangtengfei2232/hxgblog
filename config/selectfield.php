<?php
return [
    'comment' => [
        'id_field'        => 'come_id',
        'father_id_field' => 'come_father_id',
        'table_name'      => 'comment',
        'model_name'      => \App\Model\Comment::class,
        'count'           => 'come_count',
        'child_field'     => 'child_comment',
        'select_field'    => ['come_id', 'come_content', 'come_father_id',
            'comment.created_at', 'nick_name', 'head_portrait','comment.user_id']
    ],
    'leave_message' => [
        'id_field'        => 'msg_id',
        'father_id_field' => 'msg_father_id',
        'table_name'      => 'leave_message',
        'model_name'      => \App\Model\LeaveMessage::class,
        'count'           => 'msg_count',
        'child_field'     => 'child_message',
        'select_field'    => ['msg_id', 'msg_content', 'msg_father_id',
            'leave_message.created_at', 'nick_name', 'head_portrait','leave_message.user_id']
    ]
];
