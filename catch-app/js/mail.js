//send mail on click of submit button
$("#contact_form").submit(function(event) {
                event.preventDefault();
               //$('#loader').css('display', 'block');

               //fetch data from input fields
                var data = {
                    name: $("#name").val(),
                    email: $("#email").val(),
                    //number: $("#number").val(),
                    //subject: $("#subject").val(),
                    message: $("#message").val(),
                };
                console.log(data);
                $.ajax({
                    type: "POST",
                    url: "email-php.php",
                    dataType: "json",
                    data: data,
                    success: function(res) {

                        if (res.success == '0') 
                        {
                            if (res.type == 4)
                            {
                               	$(".error_name").addClass("red");
                                alert("Name is required");
                                return false;
                            }
                            else{
                                $(".error_name").removeClass("red");
                            }

                            if (res.type == 1)
                            {
                               //$('#loader').css('display', 'none');
                                $(".error_email").addClass("red");
                                alert("Email is required");
                                return false;
                            }
                            else{
                                $(".error_email").removeClass("red");
                            }

                            if (res.type == 2) {
                                //$('#loader').css('display', 'none');
                                alert(res.message);
                                return false;
                            }
                        }
                        else{
                           document.getElementById("contact_form").reset(); 
                              
                                //$('.success').css('display', 'block');
                                //$('#submitButton').html('Thanks');
                               // $('#submitButton').css('background-color','green');
                                //$(".success").fadeOut(10000);
                                alert("Thanks for submitting");
                                $(".error_name").removeClass("red");
                                $(".error_email").removeClass("red");
                                 location.reload(); 
                        }
                    }
                });
            });  