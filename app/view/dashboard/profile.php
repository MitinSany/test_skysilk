<div class="container">
<form method="POST">
    <div class="form-group">
        <label for="firstName">First Name</label>
        <input class="form-control" id="firstName" name="firstName" aria-describedby="firstNameHelp" placeholder="First Name"
            value="<?= $param['userData']['firstName'] ?? '';?>">
        <small id="firstNameHelp" class="form-text text-muted">Please enter you first name</small>
    </div>
    <div class="form-group">
        <label for="lastName">Last Name</label>
        <input class="form-control" id="lastName" name="lastName" aria-describedby="lastNameHelp" placeholder="Last Name"
               value="<?= $param['userData']['lastName'] ?? '';?>">
        <small id="lastNameHelp" class="form-text text-muted">Please enter you last name</small>
    </div>
    <button type="submit" class="btn btn-primary">Submit</button>
</form>
</div>
