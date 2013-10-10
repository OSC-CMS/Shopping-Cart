<h1><?php echo t('step_6'); ?></h1>

<?php $type = $_SESSION['install']['type']; ?>

<?php if ($type == '1') { ?>
<form id="step-form">
    <fieldset>
	    <div class="field">
		    <label for="EMAIL_ADRESS"><?php echo t('admin_1'); ?></label>
		    <input class="input" type="text" name="EMAIL_ADRESS" id="EMAIL_ADRESS" value="admin@admin.loc">
	    </div>
        <script type="text/javascript">
	        generation();

	        // Генерация пароля
	        function generatePassword(symbols, length) {
		        var result = "";
		        for (var i=0; i<length; i++) {
			        result += symbols.charAt(Math.floor(Math.random()*symbols.length));
		        };
		        return result;
	        }

	        function generation() {
		        // Наборы символов для генерации пароля
		        var Symbols = "abcdefghjklmnopqrstuvwxyzABCDEFGHJKLMNOPQRSTUVWXYZ0123456789"
		        var output = "";

		        output += generatePassword(Symbols, 8-document.getElementById("password").value.length);
		        if (document.getElementById("password").value.length == 8)
		        {
			        clearInterval(sl);
		        }
		        document.getElementById("password").value = document.getElementById("password").value+output;
		        return true;
	        }
        </script>
        <div class="field">
	        <label for="password"><?php echo t('admin_2'); ?></label>
	        <input class="input" id="password" type="text" name="PASSWORD" />
        </div>
    </fieldset>
</form>
<?php } elseif ($type == '2') { ?>
	<p>
		<?php echo t('admin_3'); ?>
	</p>
<?php } ?>

<div class="buttons">
    <input class="button button-rounded button-flat-action" type="button" name="next" id="btn-next" value="<?php echo t('next'); ?>" onclick="submitStep()" />
</div>