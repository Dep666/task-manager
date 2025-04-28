<?php

namespace App\View\Components;

use Illuminate\View\Component;

class SectionTitle extends Component
{
    public function __construct()
    {
        // Конструктор без параметров, так как данные передаются через слоты
    }

    public function render()
    {
        return view('components.section-title');
    }
} 