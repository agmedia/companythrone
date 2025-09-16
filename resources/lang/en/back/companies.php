<?php

return [
    // Page titles
    'title'         => 'Companies',
    'title_create'  => 'Create Company',
    'title_edit'    => 'Edit Company',
    
    // Small labels
    'group_label'   => 'Group',
    'empty'         => 'No company found.',
    
    // Tabs (category groups)
    'tabs' => [
        'companies' => 'Companies',
        'blog'     => 'Blog',
        'pages'    => 'Pages',
        'footer'   => 'Footer',
    ],
    
    // Table headers
    'table' => [
        'id'      => 'ID',
        'name'    => 'Name',
        'group'   => 'Group',
        'parent'  => 'Parent',
        'sort'    => 'Sort',
        'status'  => 'Status',
        'updated' => 'Updated',
        'actions' => 'Actions',
    ],
    
    // Form labels & hints
    'form' => [
        'group'          => 'Group',
        'parent'         => 'Parent',
        'parent_hint'    => 'Leave empty for a top-level category.',
        'title'          => 'Title',
        'slug'           => 'Slug',
        'auto_slug_hint' => 'If left blank, a slug will be generated from the title.',
        'description'    => 'Description',
        'image'          => 'Image',
        'icon'           => 'Icon',
        'banner'         => 'Banner',
        'sort_order'     => 'Sort order',
        'is_active'      => 'Active',
    ],
    
    // Flash messages
    'flash' => [
        'created' => 'Company created.',
        'updated' => 'Company updated.',
        'deleted' => 'Company deleted.',
    ],
    
    // Dialogs
    'confirm_delete' => 'Delete this company? This action cannot be undone.',
];
