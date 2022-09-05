<<?php

use function Pest\Livewire\livewire;

it('property displays the results and options', function (string $component, object $params) {
    livewire($component)
        ->call($params->theme)
        ->assertSeeHtmlInOrder([
            '<option value="contains_not">Does not contain</option>',
            '<option value="ends_with">Ends with</option>',
            '</select>',
        ])
        ->assertDontSeeHtml('<option value="is">Is</option>')
        ->assertDontSeeHtml('<option value="is_not">Is not</option>')
        ->assertDontSeeHtml('<option value="starts_with">Starts with</option>')
        ->assertDontSeeHtml('<option value="is_empty">Is empty</option>')
        ->assertDontSeeHtml('<option value="is_not_empty">Is not empty</option>')
        ->assertDontSeeHtml('<option value="is_null">Is null</option>')
        ->assertDontSeeHtml('<option value="is_not_null">Is not null</option>')
        ->assertDontSeeHtml('<option value="is_blank">Is blank</option>')
        ->assertDontSeeHtml('<option value="is_not_blank">Is not blank</option>');
})->with('DishTableAllowedFilters');
