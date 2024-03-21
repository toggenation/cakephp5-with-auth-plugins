<?php

/**
 * @var \App\View\AppView $this
 */
?>
<?= $this->Form->create($form); ?>
<?= $this->Form->control('clear', ['type' => 'checkbox']); ?>
<?= $this->Form->control('name'); ?>
<?= $this->Form->control('address'); ?>
<?= $this->Form->control('active', ['type' => 'checkbox']); ?>
<?= $this->Form->submit('Save'); ?>
<?php $this->Form->end(); ?>