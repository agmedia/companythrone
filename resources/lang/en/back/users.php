<?php

return [
    // ===== Existing top-level =====
    'title'          => 'Users',
    'title_create'   => 'Create User',
    'title_edit'     => 'Edit User',
    'role_label'     => 'Role',
    'confirm_delete' => 'Delete this user? This action cannot be undone.',

    // ===== Added for compatibility with new blades =====
    'page_titles' => [
        'create'   => 'Create User',     // mirrors title_create
        'edit'     => 'Edit User',       // mirrors title_edit
        'password' => 'Change Password',
    ],

    'sections' => [
        'basic_info'  => 'Basic Info',
        'password'    => 'Password',
        'role_status' => 'Role & Status',
        'avatar'      => 'Avatar',
    ],

    // Keep your existing 'form' group (used in some places)
    'form' => [
        'role'            => 'Role',
        'status'          => 'Status',
        'fname'           => 'First name',
        'lname'           => 'Last name',
        'email'           => 'Email',
        'password'        => 'Password',
        'password_hint'   => 'Leave blank to keep current password.',
        'address'         => 'Address',
        'zip'             => 'ZIP',
        'city'            => 'City',
        'state'           => 'State',
        'phone'           => 'Phone',
        'bio'             => 'Bio',
        'avatar'          => 'Avatar',
        'remove_avatar'   => 'Remove avatar',
    ],

    // New: field labels used by the updated blades
    'fields' => [
        'role'                  => 'Role',
        'status'                => 'Status',
        'fname'                 => 'First name',
        'lname'                 => 'Last name',
        'email'                 => 'Email',
        'password'              => 'New password',
        'password_confirmation' => 'Confirm password',
        'current_password'      => 'Current password',
        'address'               => 'Address',
        'zip'                   => 'ZIP',
        'city'                  => 'City',
        'state'                 => 'State',
        'phone'                 => 'Phone',
        'bio'                   => 'Bio',
        'avatar'                => 'Avatar',
        'social'                => 'Social',
    ],

    'hints' => [
        'leave_blank_keep' => 'leave blank to keep existing',
        'avatar_limit'     => 'Max 4MB. JPG, PNG, WEBP.',
    ],

    // ===== Existing tabs =====
    'tabs' => [
        'master'   => 'Master',
        'admin'    => 'Admin',
        'manager'  => 'Manager',
        'editor'   => 'Editor',
        'customer' => 'Customer',
    ],

    // ===== Existing table =====
    'table' => [
        'user'    => 'User',
        'email'   => 'Email',
        'phone'   => 'Phone',
        'city'    => 'City',
        'status'  => 'Status',
        'updated' => 'Updated',
        'actions' => 'Actions',
    ],

    // ===== Flash messages (added password_updated) =====
    'flash' => [
        'created'          => 'User created.',
        'updated'          => 'User updated.',
        'deleted'          => 'User deleted.',
        'password_updated' => 'Password updated.',
    ],
];
