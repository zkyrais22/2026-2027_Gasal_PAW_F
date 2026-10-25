<?php
// ambil isian sebelumnya (jika ada) supaya tidak hilang saat form tampil ulang
$surname = isset($_POST['surname']) ? htmlspecialchars($_POST['surname']) : '';
$email   = isset($_POST['email'])   ? htmlspecialchars($_POST['email'])   : '';
$address = isset($_POST['address']) ? htmlspecialchars($_POST['address']) : '';
$state   = isset($_POST['state'])   ? $_POST['state'] : '-1';
$sex     = isset($_POST['sex'])     ? $_POST['sex']   : '';
?>
<fieldset>
<legend>Person Details</legend>
<form name="myForm" action="processData_form2.php" method="POST">
    <div class="field">
        <label for="surname">Surname</label>
        <input type="text" id="surname" name="surname" size="31" value="<?php echo $surname; ?>"/>
    </div>
    <div class="field">
        <label for="email">Email address</label>
        <input type="text" id="email" name="email" size="31" value="<?php echo $email; ?>"/>
    </div>
    <div class="field">
        <label for="passwd">Password</label>
        <input type="password" id="passwd" name="passwd" size="31"/>
    </div>
    <div class="field">
        <label for="address">Street Address</label>
        <textarea id="address" name="address" rows="3" cols="22"><?php echo $address; ?></textarea>
    </div>
    <div class="field">
        <label for="state">State</label>
        <select id="state" name="state">
            <option value="-1" <?php if ($state == '-1') echo 'selected'; ?>>[choose your state]</option>
            <option value="1" <?php if ($state == '1') echo 'selected'; ?>>Australian Capital Territory</option>
            <option value="2" <?php if ($state == '2') echo 'selected'; ?>>Queensland</option>
            <option value="3" <?php if ($state == '3') echo 'selected'; ?>>Victoria</option>
            <option value="4" <?php if ($state == '4') echo 'selected'; ?>>New South Wales</option>
            <option value="5" <?php if ($state == '5') echo 'selected'; ?>>Tasmania</option>
            <option value="6" <?php if ($state == '6') echo 'selected'; ?>>South Australia</option>
            <option value="7" <?php if ($state == '7') echo 'selected'; ?>>Western Australia</option>
            <option value="8" <?php if ($state == '8') echo 'selected'; ?>>Northern Territory</option>
        </select>
    </div>
    <div class="field">
        <input type="hidden" name="country" value="Australia"/>
    </div>
    <div class="field">
        <label>Gender</label>
        <input type="radio" name="sex" value="male" <?php if ($sex == 'male') echo 'checked'; ?>/>Male
        <input type="radio" name="sex" value="female" <?php if ($sex == 'female') echo 'checked'; ?>/>Female
    </div>
    <div class="field">
        <label for="vegetarian">Vegetarian?</label>
        <input type="checkbox" id="vegetarian" name="vegetarian" <?php if (isset($_POST['vegetarian'])) echo 'checked'; ?>/>
    </div>
    <div class="field">
        <label>&nbsp;</label>
        <input type="submit" value="Submit" name="submit"/><input type="reset" value="Reset" name="reset"/>
    </div>
</form>
</fieldset>