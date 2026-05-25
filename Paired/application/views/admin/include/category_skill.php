
<?php if(empty($skills)): ?>
    <option value="">no skill found</option>
<?php else: ?>

    <?php foreach ($skills as $skill): ?>
        <?php foreach ($user_skills as $user_skill): ?>
            <?php 
                if ($skill->id==$user_skill->skill_id) {
                    $selected='selected'; break;
                }else{
                    $selected='';
                }
             ?>
        <?php endforeach ?>
        <option  <?php echo html_escape($selected); ?> value="<?php echo html_escape($skill->id) ?>" name="skill"><?php echo html_escape($skill->skill) ?></option>
    <?php endforeach ?>
<?php endif; ?>