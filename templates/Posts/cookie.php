<?php

/**
 * @var \App\View\AppView $this
 */
?>
<p>Data in this form is NOT saved to the database base. This is a example of saving / loading data to / from into an encrypted cookie.</p>
<p>Use the clear checkbox to clear (or delete) the cookie and hence clear the form</p>
<p>Open dev tools and view the encrypted cookie its name is <strong>form</strong></p>

<?= $this->Form->create($form); ?>
<?= $this->Form->control('clear', [
    'type' => 'checkbox'
]); ?>
<?= $this->Form->control('name'); ?>
<?= $this->Form->control('address'); ?>
<?= $this->Form->control('active', ['type' => 'checkbox']); ?>
<?= $this->Form->submit('Save'); ?>
<?php $this->Form->end(); ?>