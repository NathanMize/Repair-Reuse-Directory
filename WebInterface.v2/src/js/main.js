
//MODAL CONTROL

$('#bus-delete-warning').on('show.bs.modal', function (event) {
    var button = $(event.relatedTarget); // Button that triggered the modal

    var bus_name = button.data('name'); // Extract info from data-* attributes
    var bus_id = button.data('id');

    var modal = $(this);

    modal.find('.modal-header h4').text('Confirm Delete: ' + bus_name);
    modal.find('.modal-body p').text('Are you certain you want'
        + ' to delete this business record?');
    
    $('#bus-delete').replaceWith('<button type="button" id="bus-delete" '
        + 'class="btn btn-warning" onclick="deleteBusiness(' + bus_id 
        + ')">Delete Business</button>');
});

$('#cat-delete-warning').on('show.bs.modal', function (event) {
    var button = $(event.relatedTarget); // Button that triggered the modal

    var cat_name = button.data('name'); // Extract info from data-* attributes
    var cat_id = button.data('id');

    var modal = $(this);

    modal.find('.modal-header h4').text('Confirm Delete: ' + cat_name);
    modal.find('.modal-body p').text('Warning: Deleting category will '
        + 'delete all associated items from database.');

    $('#cat-delete').replaceWith('<button type="button" id="cat-delete" '
        + 'class="btn btn-warning" onclick="deleteCategory(' + cat_id 
        + ')">Delete Category</button>');
});

$('#item-delete-warning').on('show.bs.modal', function (event) {
    var button = $(event.relatedTarget); // Button that triggered the modal

    var item_name = button.data('name'); // Extract info from data-* attributes
    var item_id = button.data('id');

    var modal = $(this);

    modal.find('.modal-header h4').text('Confirm Delete: ' + item_name);
    modal.find('.modal-body p').text('Are you certain you want to delete '
        + 'this item record?');

    $('#item-delete').replaceWith('<button type="button" id="item-delete" '
        + 'class="btn btn-warning" onclick="deleteItem(' + item_id 
        + ')">Delete Item</button>');
});





//CHECKBOX SELECTION CONTROL
$('.category-checkbox').change(function() {
    if($(this).is(':checked')) {
        //check all items in category
        $(this).parent().parent().parent()
            .find('.items-grouping')
            .find(':checkbox').each(function() {
            $(this).prop('checked', true);
        });
    }
    else{
        //uncheck all items in category
         $(this).parent().parent().parent()
            .find('.items-grouping')
            .find(':checkbox').each(function() {
            $(this).prop('checked', false);
        });
    }
});

$(function(){
    $('.items-grouping').each(function(){
        if($(this).children().length < 1){
            return;
        }

        var totalCount = 0;
        var checkedCount = 0;
        $(this).find(':checkbox').each(function(){
            totalCount++;
            if($(this).is(':checked')){
                checkedCount++;
            }
        });

        if(totalCount == checkedCount){
            $(this).parent().children('.checkbox')
            .find('.category-checkbox').prop('checked', true);
        }
        else{
            $(this).parent().children('.checkbox')
            .find('.category-checkbox').prop('checked', false);
        }
    });
});


$('.items-grouping').change(function() {
    $('.items-grouping').each(function(){
        if($(this).children().length < 1){
            return;
        }

        var totalCount = 0;
        var checkedCount = 0;
        $(this).find(':checkbox').each(function(){
            totalCount++;
            if($(this).is(':checked')){
                checkedCount++;
            }
        });

        if(totalCount == checkedCount){
            $(this).parent().children('.checkbox')
            .find('.category-checkbox').prop('checked', true);
        }
        else{
            $(this).parent().children('.checkbox')
            .find('.category-checkbox').prop('checked', false);
        }
    });
});





//AJAX CALLS

function addBusiness() {

    var bus_name = $('#bus-name').val();
    var bus_street = $('#bus-street').val();
    var bus_city = $('#bus-city').val();
    var bus_state = $('#bus-state').val();
    var bus_zip = $('#bus-zip').val();
    var bus_phone = $('#bus-phone').val();
    var bus_website = $('#bus-website').val();
    var bus_info = $('#bus-info').val();
    var bus_hours = $('#bus-hours').val();

    var items = [];
    var items_resell = [];
    var items_repair = [];

    $('.item-checkbox').each(function(){
        if($(this).is(":checked")) {
            items.push($(this).val());
        }
    });

    $('.item-resell-checkbox').each(function(){
        if($(this).is(":checked")) {
            items_resell.push($(this).val());
        }
    });

    $('.item-repair-checkbox').each(function(){
        if($(this).is(":checked")) {
            items_repair.push($(this).val());
        }
    });

    if (items === undefined){
        items = [];
    }

    if (items_resell === undefined){
        items_resell = [];
    }

    if (items_repair === undefined){
        items_repair = [];
    }

    var request = $.ajax({
        method: 'POST',
        url: './controls/db_addBusiness.php',
        data:{
            'name': bus_name,
            'street': bus_street,
            'city': bus_city,
            'state': bus_state,
            'zip': bus_zip,
            'phone': bus_phone,
            'website': bus_website,
            'info': bus_info,
            'items': items,
            'items-resell': items_resell,
            'items-repair': items_repair,
            'hours': bus_hours
        }
    });


    request.done(function(msg) {
        var obj = JSON.parse(msg);

        if(obj.message == 'success'){
            $('#result').html('<div class="alert alert-success col-sm-8"'
                + ' role="alert" style="width: 550px;"><strong>Success! '
                + '</strong>' + bus_name 
                + ' has been added to database.</div>');
        }
        else if(obj.message == 'error'){
            if(obj.issue == 'no name'){
                $('#result').html('<div class="alert alert-danger col-sm-8" '
                    + 'role="alert" style="width: 550px;"><strong>Field '
                    + 'Required. </strong>Please enter a business name.</div>');
            }
            else if(obj.issue == 'incorrect zip'){
                $('#result').html('<div class="alert alert-danger col-sm-8" '
                    + 'role="alert" style="width: 550px;"><strong>Invalid '
                    + 'Input. </strong>Please enter a 5-digit numeric zip '
                    + 'code.</div>');
            }
            else if(obj.issue == 'duplicate name'){
                $('#result').html('<div class="alert alert-danger col-sm-8" '
                    + 'role="alert" style="width: 550px;"><strong>Whoops!'
                    + '</strong> That business has already been added to the '
                    + 'database.  Please choose a different name.</div>');
            }
        }
    });

    request.fail(function(jqXHR, textStatus) {
        $('#result').html('Request failed: ' + textStatus);
    });
}



function addCategory() {
    var cat_name = $('#cat-name').val();

    var request = $.ajax({
        method: 'POST',
        url: './controls/db_addCategory.php',
        data:{
            'name': cat_name
        }
    });


    request.done(function(msg) {
        var obj = JSON.parse(msg);

        if(obj.message == 'success'){
            $('#result').html('<div class="alert alert-success col-sm-8" '
                + 'role="alert" style="width: 550px;"><strong>Success! '
                + '</strong>' + cat_name 
                + ' has been added to database.</div>');
        }
        else if(obj.message == 'error'){
            if(obj.issue == 'no name'){
                $('#result').html('<div class="alert alert-danger col-sm-8" '
                    + 'role="alert" style="width: 550px;"><strong>Field '
                    + 'Required. </strong>Please enter a category name.</div>');
            }
            else if(obj.issue == 'duplicate name'){
                $('#result').html('<div class="alert alert-danger col-sm-8" '
                    + 'role="alert" style="width: 550px;"><strong>Whoops!'
                    + '</strong> That category has already been added to the '
                    + 'database.  Please choose a different name.</div>');
            }
        }
    });

    request.fail(function(jqXHR, textStatus) {
        $('#result').html('Request failed: ' + textStatus);
    });
}


function addItem() {
    var item_name = $('#item-name').val();
    var category = $('#item-cat').val();

    if (category === null){
        category = "";
    }    

    var request = $.ajax({
        method: 'POST',
        url: './controls/db_addItem.php',
        data:{
            'name': item_name,
            'cat': category
        }
    });


    request.done(function(msg) {
        var obj = JSON.parse(msg);

        if(obj.message == 'success'){
            $('#result').html('<div class="alert alert-success col-sm-8" '
                + 'role="alert" style="width: 550px;"><strong>Success! '
                + '</strong>' + item_name 
                + ' has been added to database.</div>');
        }
        else if(obj.message == 'error'){
            if(obj.issue == 'no name'){
                $('#result').html('<div class="alert alert-danger col-sm-8" '
                    + 'role="alert" style="width: 550px;"><strong>Field '
                    + 'Required. </strong>Please enter an item name.</div>');
            }
            else if(obj.issue == 'duplicate name'){
                $('#result').html('<div class="alert alert-danger col-sm-8" '
                    + 'role="alert" style="width: 550px;"><strong>Whoops!'
                    + '</strong> That item has already been added to the '
                    + 'database.  Please choose a different name.</div>');
            }
        }
    });

    request.fail(function(jqXHR, textStatus) {
        $('#result').html('Request failed: ' + textStatus);
    });
}





function editBusiness(id) {
    var bus_name = $('#bus-name').val();
    var bus_street = $('#bus-street').val();
    var bus_city = $('#bus-city').val();
    var bus_state = $('#bus-state').val();
    var bus_zip = $('#bus-zip').val();
    var bus_phone = $('#bus-phone').val();
    var bus_website = $('#bus-website').val();
    var bus_info = $('#bus-info').val();
    var bus_hours = $('#bus-hours').val();

    var items = [];
    var items_resell = [];
    var items_repair = [];

    $('.item-checkbox').each(function(){
        if($(this).is(":checked")) {
            items.push($(this).val());
        }
    });

    $('.item-resell-checkbox').each(function(){
        if($(this).is(":checked")) {
            items_resell.push($(this).val());
        }
    });

    $('.item-repair-checkbox').each(function(){
        if($(this).is(":checked")) {
            items_repair.push($(this).val());
        }
    });

    if (items === undefined){
        items = [];
    }

    if (items_resell === undefined){
        items = [];
    }

    if (items_repair === undefined){
        items = [];
    }

    var request = $.ajax({
        method: 'POST',
        url: './controls/db_editBusiness.php',
        data:{
            'name': bus_name,
            'street': bus_street,
            'city': bus_city,
            'state': bus_state,
            'zip': bus_zip,
            'phone': bus_phone,
            'website': bus_website,
            'info': bus_info,
            'items': items,
            'items-resell': items_resell,
            'items-repair': items_repair,
            'id': id,
            'hours': bus_hours
        }
    });


    request.done(function(msg) {
        var obj = JSON.parse(msg);

        if(obj.message == 'success'){
            $('#result').html('<div class="alert alert-success col-sm-8" '
                + 'role="alert" style="width: 550px;"><strong>Success! '
                + '</strong>' + bus_name 
                + ' has been updated in database.</div>');
        }
        else if(obj.message == 'error'){
            if(obj.issue == 'no name'){
                $('#result').html('<div class="alert alert-danger col-sm-8" '
                    + 'role="alert" style="width: 550px;"><strong>Field '
                    + 'Required. </strong>Please enter a business name.</div>');
            }
            else if(obj.issue == 'incorrect zip'){
                $('#result').html('<div class="alert alert-danger col-sm-8" '
                    + 'role="alert" style="width: 550px;"><strong>Invalid '
                    + 'Input. </strong>Please enter a 5-digit numeric zip '
                    + 'code.</div>');
            }
            else if(obj.issue == 'duplicate name'){
                $('#result').html('<div class="alert alert-danger col-sm-8" '
                    + 'role="alert" style="width: 550px;"><strong>Whoops!'
                    + '</strong> That business has already been added to the '
                    + 'database.  Please choose a different name.</div>');
            }
        }
    });

    request.fail(function(jqXHR, textStatus) {
        $('#result').html('Request failed: ' + textStatus);
    });
}


function editCategory(id) {
    var cat_name = $('#cat-name').val();

    var request = $.ajax({
        method: 'POST',
        url: './controls/db_editCategory.php',
        data:{
            'name': cat_name,
            'id': id
        }
    });


    request.done(function(msg) {
        var obj = JSON.parse(msg);

        if(obj.message == 'success'){
            $('#result').html('<div class="alert alert-success col-sm-8" '
                + 'role="alert" style="width: 550px;"><strong>Success! '
                + '</strong>' + cat_name 
                + ' has been updated in database.</div>');
        }
        else if(obj.message == 'error'){
            if(obj.issue == 'no name'){
                $('#result').html('<div class="alert alert-danger col-sm-8" '
                    + 'role="alert" style="width: 550px;"><strong>Field '
                    + 'Required. </strong>Please enter a category name.</div>');
            }
            else if(obj.issue == 'duplicate name'){
                $('#result').html('<div class="alert alert-danger col-sm-8" '
                    + 'role="alert" style="width: 550px;"><strong>Whoops!'
                    + '</strong> That category has already been added to the '
                    + 'database.  Please choose a different name.</div>');
            }
        }
    });

    request.fail(function(jqXHR, textStatus) {
        $('#result').html('Request failed: ' + textStatus);
    });
}


function editItem(id) {
    var item_name = $('#item-name').val();
    var category = $('#item-cat').val();

    if (category === null){
        category = "";
    }    

    var request = $.ajax({
        method: 'POST',
        url: './controls/db_editItem.php',
        data:{
            'name': item_name,
            'cat': category,
            'id': id
        }
    });


    request.done(function(msg) {
        var obj = JSON.parse(msg);
        
        if(obj.message == 'success'){
            $('#result').html('<div class="alert alert-success col-sm-8" '
                + 'role="alert" style="width: 550px;"><strong>Success! '
                + '</strong>' + item_name + ' has been updated in '
                + 'database.</div>');
        }
        else if(obj.message == 'error'){
            if(obj.issue == 'no name'){
                $('#result').html('<div class="alert alert-danger col-sm-8" '
                    + 'role="alert" style="width: 550px;"><strong>Field '
                    + 'Required. </strong>Please enter an item name.</div>');
            }
            else if(obj.issue == 'duplicate name'){
                $('#result').html('<div class="alert alert-danger col-sm-8" '
                    + 'role="alert" style="width: 550px;"><strong>Whoops!'
                    + '</strong> That item has already been added to the '
                    + 'database.  Please choose a different name.</div>');
            }
        }
    });

    request.fail(function(jqXHR, textStatus) {
        $('#result').html('Request failed: ' + textStatus);
    });
}



function deleteBusiness(id) {

    var request = $.ajax({
        method: 'POST',
        url: './controls/db_deleteBusiness.php',
        data:{
            'id': id
        }
    });


    request.done(function(msg) {
        var obj = JSON.parse(msg);

        if(obj.data == 'business table'){
            $('#business-data').html(obj.html);
        }
    });

    request.fail(function(jqXHR, textStatus) {
        $('#business-data').html('Request failed: ' + textStatus);
    });

    $('#bus-delete-warning').modal('hide');
}


function deleteCategory(id) {   
    
    var request = $.ajax({
        method: 'POST',
        url: './controls/db_deleteCategory.php',
        data:{
            'id': id
        }
    });


    request.done(function(msg) {
        var obj = JSON.parse(msg);

        if(obj[0].data == 'business table'){
            $('#business-data').html(obj[0].html);
        }
        if(obj[1].data == 'category table'){
            $('#category-data').html(obj[1].html);
        }
        if(obj[2].data == 'item table'){
            $('#item-data').html(obj[2].html);
        }
    });

    request.fail(function(jqXHR, textStatus) {
        $('#category-data').html('Request failed: ' + textStatus);
    });

    $('#cat-delete-warning').modal('hide');
}



function deleteItem(id) {   

    var request = $.ajax({
        method: 'POST',
        url: './controls/db_deleteItem.php',
        data:{
            'id': id
        }
    });

    request.done(function(msg) {
        var obj = JSON.parse(msg);

        if(obj[0].data == 'business table'){
            $('#business-data').html(obj[0].html);
        }
        if(obj[1].data == 'category table'){
            $('#category-data').html(obj[1].html);
        }
        if(obj[2].data == 'item table'){
            $('#item-data').html(obj[2].html);
        }
    });

    request.fail(function(jqXHR, textStatus) {
        $('#item-data').html('Request failed: ' + textStatus);
    });

    $('#item-delete-warning').modal('hide');
}







//LOGIN & ACCOUNT CHANGE
function login() {   
    var username = $('#username').val();
    var password = $('#password').val();

    var request = $.ajax({
        method: 'POST',
        url: './controls/db_login.php',
        data:{
           'username': username,
           'password': password
        }
    });

    request.done(function(msg) {
        var obj = JSON.parse(msg);

        if(obj.message == 'account page'){
            window.location.href = './account.php';
        }
        else if(obj.message == 'home page'){
            window.location.href = './home.php';
        }
        else if(obj.message == 'error'){
            if(obj.issue == 'incorrect info'){
                $('#username').val("");
                $('#password').val("");
                $('#result').html('<div class="alert alert-danger" '
                    + 'role="alert"><strong>Whoops. </strong>Your username or '
                    + 'password are not recognized.</div>');
            }
            else if(obj.issue == 'no username'){
                $('#result').html('<div class="alert alert-danger" '
                    + 'role="alert"><strong>Field Required. </strong>Please '
                    + 'enter a username.</div>');
            }
            else if(obj.issue == 'no password'){
                $('#result').html('<div class="alert alert-danger" '
                    + 'role="alert"><strong>Field Required. </strong>Please '
                    + 'enter a password.</div>');
            }
        }
    });

    request.fail(function(jqXHR, textStatus) {
        $('#result').html('Request failed: ' + textStatus);
    });
}


function changeUsername() {   
    var username = $('#new-username').val();

    var request = $.ajax({
        method: 'POST',
        url: './controls/db_editUsername.php',
        data:{
           'username': username
        }
    });

    request.done(function(msg) {
        var obj = JSON.parse(msg);

        if(obj.message == 'success'){
            $('#result-username-update').html("<p>" + obj.username + "</p>");
            $('#result-username').html("");
            $('#new-username').val("");
        }
        else if (obj.message == 'error') {
            if(obj.issue == 'incorrect characters'){
                $('#result-username').html('<div class="alert alert-danger '
                    + 'col-sm-8" role="alert" style="width: 550px;"><strong>'
                    + 'Invalid. </strong>Do not include a space, &#039, &quot, '
                    + '&amp, &lt, or &gt characters.</div>');
                $('#new-username').val("");
            }
            else if(obj.issue == 'incorrect length'){
                $('#result-username').html('<div class="alert alert-danger '
                    + 'col-sm-8" role="alert" style="width: 550px;"><strong>'
                    + 'Invalid. </strong>Please enter a username between 6 and '
                    + '20 characters.</div>');
                $('#new-username').val("");
            } 
        }
    });

    request.fail(function(jqXHR, textStatus) {
        $('#result-username').html('Request failed: ' + textStatus);
    });
}



function changePassword() {   
    var password1 = $('#password1').val();
    var password2 = $('#password2').val();

    var request = $.ajax({
        method: 'POST',
        url: './controls/db_editPassword.php',
        data:{
           'password1': password1,
           'password2': password2
        }
    });

    request.done(function(msg) {
        var obj = JSON.parse(msg);

        if(obj.message == 'success'){
                $('#result-password').html('<div class="alert alert-success '
                    + 'col-sm-8" role="alert" style="width: 550px;"><strong>'
                    + 'Success! </strong>Password has been updated.</div>');
                $('#password1').val("");
                $('#password2').val("");
        }

        else if(obj.message == 'error'){
            if(obj.issue == 'no match'){
                $('#result-password').html('<div class="alert alert-danger '
                    + 'col-sm-8" role="alert" style="width: 550px;"><strong>'
                    + 'Invalid. </strong>Passwords do not match.</div>');
                $('#password1').val("");
                $('#password2').val("");
            }
            else if(obj.issue == "incorrect length"){
                $('#result-password').html('<div class="alert alert-danger '
                    + 'col-sm-8" role="alert" style="width: 550px;"><strong>'
                    + 'Invalid. </strong>Please enter a password between 10 '
                    + 'and 20 characters.</div>');
                $('#password1').val("");
                $('#password2').val("");
            }
        }
    });

    request.fail(function(jqXHR, textStatus) {
        $('#result-password').html('Request failed: ' + textStatus);
    });
}