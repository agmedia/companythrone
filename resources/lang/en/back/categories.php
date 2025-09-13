<?php

return [
    // Page titles
    'title'         => 'Categories',
    'title_create'  => 'Create Category',
    'title_edit'    => 'Edit Category',
    
    // Small labels
    'group_label'   => 'Group',
    'empty'         => 'No categories found.',
    
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
        'created' => 'Category created.',
        'updated' => 'Category updated.',
        'deleted' => 'Category deleted.',
    ],
    
    // Dialogs
    'confirm_delete' => 'Delete this category? Subcategories will also be deleted. This action cannot be undone.',
];
