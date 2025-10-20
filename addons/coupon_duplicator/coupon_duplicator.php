<?php
if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

use WHMCS\Database\Capsule;

/**
 * Module configuration
 */
function coupon_duplicator_config(): array
{
    return [
        'name' => 'Coupon Duplicator',
        'description' => 'Duplicate a WHMCS promotion coupon multiple times with random or custom codes.',
        'version' => '1.0',
        'author' => 'Arafat Islam <https://github.com/arafatkn/whmcs-modules>',
        'fields' => [],
    ];
}

/**
 * Module activation
 */
function coupon_duplicator_activate(): array
{
    return ['status' => 'success', 'description' => 'Coupon Duplicator activated'];
}

/**
 * Admin area output
 */
function coupon_duplicator_output($vars)
{
    $action = $_POST['action'] ?? '';
    echo '<h2>Duplicate Coupons</h2>';

    if ($action === 'duplicate') {
        $couponId = (int) $_POST['coupon_id'];
        $prefix = trim($_POST['prefix'] ?? '');
        $random = (int) ($_POST['random'] ?? 8);
        $suffix = trim($_POST['suffix'] ?? '');
        $codesList = trim($_POST['codes_list'] ?? '');
        $copies = (int) ($_POST['copies'] ?? 1);

        if (empty($codesList) && $random < 1) {
            echo '<div class="errorbox">Random character size must be at least one.</div>';
            return;
        }

        if (!$couponId) {
            echo '<div class="errorbox">Coupon ID is required.</div>';
            return;
        }

        $original = Capsule::table('tblpromotions')->find($couponId);
        if (!$original) {
            echo '<div class="errorbox">Coupon not found.</div>';
            return;
        }

        $codes = [];
        if ($codesList) {
            $codes = preg_split('/\r\n|\r|\n/', $codesList);
            $codes = array_filter(array_map('trim', $codes));
        } else {
            for ($i = 0; $i < $copies; $i++) {
                $rand = strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, $random));
                $codes[] = $prefix . $rand . $suffix;
            }
        }

        $inserted = [];
        foreach ($codes as $code) {
            if (Capsule::table('tblpromotions')->where('code', $code)->exists()) {
                continue; // Skip duplicates
            }

            $data = (array) $original;
            unset($data['id']);
            $data['code'] = $code;

            $newId = Capsule::table('tblpromotions')->insertGetId($data);
            $inserted[] = ['id' => $newId, 'code' => $code];
        }

        echo '<div class="infobox"><strong>Success!</strong> Created ' . count($inserted) . ' coupons.</div>';
        echo '<ul>';
        foreach ($inserted as $row) {
            $editUrl = 'configpromotions.php?action=manage&id=' . $row['id'];
            echo '<li>ID: ' . $row['id'] . ' — Code: <b>' . $row['code'] . '</b> — <a href="' . $editUrl . '" target="_blank">Edit</a></li>';
        }
        echo '</ul>';
    }

    echo <<<HTML
    <form method="post" id="couponDuplicatorForm">
        <input type="hidden" name="action" value="duplicate" />
        <table class="form" width="100%" border="0" cellspacing="2" cellpadding="3">
            <tr>
                <td class="fieldlabel">Coupon ID</td>
                <td class="fieldarea">
                    <input type="text" name="coupon_id" size="10" class="form-control" required />
                </td>
            </tr>
            <tr>
                <td class="fieldlabel">Prefix</td>
                <td class="fieldarea">
                    <input type="text" name="prefix" class="form-control" size="20" />
                </td>
            </tr>
            <tr>
                <td class="fieldlabel">Number of Random Characters</td>
                <td class="fieldarea">
                    <input type="number" name="random" class="form-control" size="20" value="8" min="1" />
                </td>
            </tr>
            <tr>
                <td class="fieldlabel">Suffix</td>
                <td class="fieldarea">
                    <input type="text" name="suffix" class="form-control" size="20" />
                </td>
            </tr>
            <tr>
                <td class="fieldlabel">Number of Copies</td>
                <td class="fieldarea">
                    <input type="number" name="copies" class="form-control" value="1" min="1" />
                </td>
            </tr>
            <tr>
                <td class="fieldlabel">Custom Codes (one per line)</td>
                <td class="fieldarea">
                    <textarea name="codes_list" id="codes_list" rows="5" cols="50" class="form-control" placeholder="Enter custom codes here..."></textarea>
                    <div id="noteBox" style="margin-top:5px;color:#a94442;visibility:hidden;font-weight:bold;">
                        ⚠️ Custom codes provided — Prefix, Suffix, and Number of Copies will be ignored.
                    </div>
                </td>
            </tr>
        </table>
        <p><input type="submit" value="Duplicate Coupon" class="btn btn-primary"></p>
    </form>

    <script>
        const codesList = document.getElementById('codes_list');
        const noteBox = document.getElementById('noteBox');

        codesList.addEventListener('input', function() {
            if (this.value.trim().length > 0) {
                noteBox.style.visibility = 'visible';
            } else {
                noteBox.style.visibility = 'hidden';
            }
        });
    </script>
HTML;
}
