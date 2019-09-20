Checker = function () {

    ch = this;

    this.checkups = {};

    this.setCheckups = function (checkups)
    {

        ch.checkups = checkups;

    }

    this.updateCheckups = function (key, values)
    {

        if (ch.checkups[key]) {

            $.each(values, function(k, val){

                ch.checkups[key][k] = val;

            });

        }

    }

    this.check = function (data)
    {

        var results = {};
        var errors = [];

        $.each(ch.checkups, function(name, val) {

            var errormessages = val.errors ? val.errors : [];

            $.each(val, function(parameter, test) {

                if (parameter == 'required') {

                    if (!data[name]) {

                        if (errormessages[parameter]) {

                            errors.push(errormessages[parameter]);

                        }

                    }

                }

                if (parameter == 'match') {

                    var match = decodeURIComponent(test);
                    var regexp = new RegExp(match);

                    if (data[name]) {

                        if (!data[name].match(regexp)) {

                            if (errormessages[parameter]) {

                                errors.push(errormessages[parameter]);

                            }

                        }
    
                    }
                    
                }

                if (parameter == 'length') {

                    if (data[name]) {

                        if (data[name].length > test) {

                            if (errormessages[parameter]) {

                                errors.push(errormessages[parameter]);

                            }

                        }

                    }
                    
                }

                if (parameter == 'possible') {

                    if (data[name]) {

                        var entry = function(val, array){

                            if (!array.includes(val)) {

                                return false;

                            }

                            return true;

                        };

                        var fit = true;

                        if (Array.isArray(data[name])) {
    
                            $.each(data[name], function(i, val){

                                if(!(fit = entry(val, test))) {
                                    return false;
                                }

                            });
    
                        } else {
    
                            fit = entry(data[name], test);
    
                        }

                        if (!fit) {

                            if (errormessages[parameter]) {

                                errors.push(errormessages[parameter]);

                            }

                        }

                    }

                }

            });

        });

        if (errors.length) {

            results.errors = errors;

        }

        return results;

    }

}

Depender = function (checker) {

    dep = true;

    this.depends = {};

    this.setDepends = function (depends) 
    {

        dep.depends = depends;

    }

    this.getUpdates = function (name, compare, values)
    {

        var s_input = $(document).find('[name="'+name+'"]');
        var s_updates = values[compare];
        
        if (s_updates && s_updates.input) {

            checker.updateCheckups(name, s_updates.input);

            if (s_updates.input.disabled === false) {

                s_input.removeAttr('disabled');

            }
            
            if (s_updates.input.mask) {

                s_input.val('');
                s_input.inputmask({"mask": s_updates.input.mask});

            }

            if (s_updates.input.placeholder) {

                s_input.attr('placeholder', s_updates.input.placeholder);

            }

        }

    }

}
$(document).ready(function(){
    
    var checker = new Checker();
    var depender = new Depender(checker);

    $(document).find("input:text").inputmask();

    $('#creating-form').on('submit', function(){

        var form = $(this);
        var sent = form.serialize();
        var data = form.serializeArray();
        var fields = {};
        
        $.each(data, function(i, val){
            
            if (val.name) {

                if (key = val.name.match(/^([a-z\d]*)\[\]$/)) {
                    
                    name = key[1];
                    if (!fields[name]) {

                        fields[name] = [];

                    }

                    fields[name].push(val.value);

                } else {

                    fields[val.name] = val.value;

                }

            }

        });

        var results = checker.check(fields);

        if (!results.errors) {

            $.ajax({
                type: "POST",
                data: sent,
                dataType: "json",
                success: function(data){

                    console.log(data);
                    
                }
            });

        }

        return false;

    });

    $.ajax({
        type: "GET",
        url: '/tools/form.json',
        dataType: "json",
        success: function(data){

            if (data.checkups) {

                checker.setCheckups(data.checkups);
                
            }

            if (data.depends) {

                depender.setDepends(data.depends);

                $.each(data.depends, function(k, value){

                    var slave = k;

                    $.each(value, function (leading, values) {

                        $(document).on('change', '[name="'+leading+'"]', function () {
                        
                            var l_value = $(this).val();
                            
                            depender.getUpdates(slave, l_value, values);
        
                        });

                    });
    
                });
                
            }

        }
    });

});