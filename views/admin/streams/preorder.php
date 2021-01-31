<?php
    /* @var $domains array */

use yii\helpers\Html;

?>
<div class="streams-create">

    <?php
        foreach($domains as $domain) {
            ?>
                <p>
                    <span><?=$domain->name?></span>
                    <?= Html::a('Set streams order', ['order', 'domain_id' => $domain->id], ['class' => 'btn btn-success']) ?>
                </p>
            <?php
        }
    ?>

</div>
