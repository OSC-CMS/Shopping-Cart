<!DOCTYPE html>
<html>
<head>
<title><?php echo t('main_title'); ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link type="text/css" rel="stylesheet" href="css/styles.css">
<link rel="stylesheet" href="css/buttons.css">
<script type="text/javascript" src="js/jquery.js"></script>
<script type="text/javascript" src="js/install.js"></script>
<script type="text/javascript" src="js/buttons.js"></script>
</head>
<body>
    <div id="layout">
        <div id="header" class="wrapp">
            <div class="logo">
                <span>1.1.0.beta</span>
                <div class="langs">
                    <?php foreach($langs as $id){ ?>
                        <a <?php if ($id==$lang) { ?>class="selected"<?php } ?> style="background-image:url('languages/<?php echo $id; ?>/flag.png')" href="?lang=<?php echo $id; ?>"><?php echo mb_strtoupper($id); ?></a>
                    <?php } ?>
                </div>
            </div>
        </div>

        <table id="main" class="wrapp">
            <tr>
                <td id="sidebar" valign="top">
                    <ul id="steps">
                        <?php foreach($steps as $num => $step) { ?>
                            <li id="<?php echo $step['id']; ?>" <?php if($num == $current_step) { ?>class="active"<?php } ?>>
                                <?php echo $num+1; ?>. <?php echo $step['title']; ?>
                            </li>
                        <?php } ?>
                    </ul>
                </td>
                <td id="body" valign="top">
                    <?php echo $step_html; ?>
                </td>
            </tr>
        </table>

        <div id="footer" class="wrapp">
            <div id="copyright"><?php echo t('main_copyright'); ?></div>
        </div>
    </div>

    <script>
        var current_step = <?php echo $current_step; ?>;
    </script>
</body>
</html>