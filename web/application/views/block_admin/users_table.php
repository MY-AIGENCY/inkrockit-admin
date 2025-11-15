<?php if (!empty($users)) { ?>
    <table class="activity_datatable" width="100%">
        <tbody>
            <tr>
                <th>Email</th>
                <th>Company</th>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Access</th>
                <th>Action</th>
            </tr>
            <?php
            foreach ($users as $user) {
                ?>
                <tr>
                    <td><a href="/admin/users/edit/<?= $user['id'] ?>"><?= $user['email'] ?></a></td>
                    <td><?= $user['company'] ?></td>
                    <td><?= $user['first_name'] ?></td>
                    <td><?= $user['last_name'] ?></td>
                    <td width="50"><?= $user_groups[$user['group_id']] ?></td>
                    <td width="50">
                        <a class="ui-icon ui-icon-pencil left" href="/admin/users/edit/<?= $user['id'] ?>">edit</a>
                        <a class="ui-icon ui-icon-closethick left" original-title="Delete" href="/admin/users/del/<?= $user['id'] ?>">del</a>
                    </td>
                </tr>
                <?php
            }
            ?>
        </tbody>
    </table>

    <div class="content_pad text_center">
        <ul class="pagination users">
            <?php
            if (!empty($paginator)) {
                echo $paginator;
            }
            ?>
        </ul>
    </div>
<?php } else {
    ?>
    <em style="padding: 20px; line-height: 30px">*Users not found</em>    
    <?php
}?>