<?php

namespace PowerComponents\LivewirePowerGrid\Themes\Components;

class Layout
{
    use HasProperties;

    // Header
    public function container(string $container): self
    {
        $this->properties['container'] = $container;

        return $this;
    }

    public function subContainer(string $subContainer): self
    {
        $this->properties['subContainer'] = $subContainer;

        return $this;
    }

    public function actionsContainer(string $actionsContainer): self
    {
        $this->properties['actionsContainer'] = $actionsContainer;

        return $this;
    }

    public function actions(string $actions): self
    {
        $this->properties['actions'] = $actions;

        return $this;
    }

    // Table
    public function table(string $table): self
    {
        $this->properties['table'] = $table;

        return $this;
    }

    public function thead(string $thead): self
    {
        $this->properties['thead'] = $thead;

        return $this;
    }

    public function tbody(string $tbody): self
    {
        $this->properties['tbody'] = $tbody;

        return $this;
    }

    public function tr(string $tr): self
    {
        $this->properties['tr'] = $tr;

        return $this;
    }

    public function th(string $th): self
    {
        $this->properties['th'] = $th;

        return $this;
    }

    public function td(string $td): self
    {
        $this->properties['td'] = $td;

        return $this;
    }

    public function tfoot(string $tfoot): self
    {
        $this->properties['tfoot'] = $tfoot;

        return $this;
    }

    // Footer
    public function select(string $select): self
    {
        $this->properties['select'] = $select;

        return $this;
    }
}
