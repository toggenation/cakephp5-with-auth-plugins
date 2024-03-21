<?php

echo $this->Html->scriptBlock(sprintf(
    'var csrfToken = %s;',
    json_encode($this->request->getAttribute('csrfToken'))
), ['block' => true]);

$this->Html->script('ajax', ['block' => true]);
?>

<p><?= $this->Html->link(
        "Post",
        '#',
        ['onClick' => 'javascript:post(); return false;']
    ); ?></p>
<p><?= $this->Html->link(
        "Get",
        '#',
        ['onClick' => 'javascript:get(); return false;']
    ); ?></p>

<p>
<div id="email"></div>
</p>
<p>
<div id="method"></div>
</p>