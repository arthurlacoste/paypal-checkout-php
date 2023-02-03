<?php

require 'config.php';
?>
<html>

<head>
    <title>PayPal Checkout</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2/dist/tailwind.min.css" rel="stylesheet" type="text/css" />
    <link href="style.css" rel="stylesheet" type="text/css" />
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.1/jquery.min.js"></script>
    <meta charset="utf-8" />
    <link rel="apple-touch-icon" sizes="76x76" href="assets/pixel-assistant.png">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no" />
</head>

<body class="bg-white">

    <!-- Snippet -->
    <section class="antialiased bg-gray-100 text-gray-600 min-h-screen p-4">
        <div class="h-full">
            <!-- Pay component -->
            <div>
                <!-- Card background -->
                <div class="relative px-4 sm:px-6 lg:px-8 max-w-lg mx-auto">
                    <img class="rounded-t shadow-lg" src="https://images.unsplash.com/photo-1628626969172-74f95362400f?ixlib=rb-4.0.3&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=1332&q=80" width="460" height="180"
                        alt="Pay background" />
                </div>
                <!-- Card body -->
                <div class="relative px-4 sm:px-6 lg:px-8 pb-8 max-w-lg mx-auto" x-data="{ card: true }">
                    <div class="bg-white px-8 py-6 rounded-b shadow-lg">

                        <!-- Card header -->
                        <div class="text-center mb-6">
                            <h1 class="text-xl leading-snug text-gray-800 font-semibold mb-2">
                                Donate to our cause
                            </h1>
                            <p class="text-gray-600 text-sm">
                                We are a non-profit organization that helps sharks.
                            </p>
                        </div>
                        <!-- Card form -->
                        <div x-show="card">
                            <div class="space-y-4">
                                <!-- Card Number -->
                                <div>
                                    <label class="block text-sm font-medium mb-1" for="card-nr">Amount to pay <span
                                            class="text-red-500">*</span></label>
                                    <input id="amount"
                                        class="text-sm text-gray-800 bg-white border rounded leading-5 py-2 px-3 border-gray-200 hover:border-gray-300 focus:border-indigo-300 shadow-sm placeholder-gray-400 focus:ring-0 w-full"
                                        type="text" value="200" placeholder="200" />
                                </div>

                            </div>

                            <div id="overlay">
                                <div id="modal-spinner">
                                    <div class="spinner"></div>
                                </div>
                            </div>

                            <!-- PayPal Checkout code -->
                            <script
                                src="https://www.paypal.com/sdk/js?client-id=<?php echo getenv('PAYPAL_CLIENT_ID'); ?>&currency=EUR&enable-funding=paylater">
                            </script>
                            <div class="mt-6 mb-6">
                                <div id="paypal-button-container"></div>
                            </div>
                            <script>
                                const fundingSources = [
                                    paypal.FUNDING.PAYPAL
                                ]

                                for (const fundingSource of fundingSources) {
                                    const paypalButtonsComponent = paypal.Buttons({
                                        fundingSource: fundingSource,
                                        // set up the transaction
                                        createOrder: (data, actions) => {

                                            const createOrderPayload = {
                                                purchase_units: [{
                                                    amount: {
                                                        value: $('#amount').val(),
                                                    },
                                                }, ],
                                            }

                                            return actions.order.create(createOrderPayload)
                                        },

                                        // finalize the transaction
                                        onApprove: (data, actions) => {
                                            return actions.order.capture().then(function(orderData) {

                                                document.getElementById("overlay").style.display = "flex";
                                                console.log('Capture result', orderData, JSON.stringify(orderData, null, 2));
                                                const transaction = orderData.purchase_units[0].payments.captures[0];

                                                // Send the transaction data to the server
                                                $.post('api.php', {
                                                    orderData: orderData,
                                                    orderID: Date.now() + Math.random()
                                                }).done(function(response) {
                                                    if(response.error !== undefined) {
                                                        alert('Error : ' . response.error);
                                                        document.getElementById("overlay").style.display = "none";
                                                    } else {
                                                        window.location.href = response.redirect;
                                                    }
                                                }).fail(function(xhr) {
                                                    console.log(xhr)
                                                    alert(xhr);
                                                });
                                            });
                                        },

                                        // handle unrecoverable errors
                                        onError: (err) => {
                                            console.error(
                                                'An error prevented the buyer from checking out with PayPal',
                                            )
                                        },
                                    })

                                    if (paypalButtonsComponent.isEligible()) {
                                        paypalButtonsComponent
                                            .render('#paypal-button-container')
                                            .catch((err) => {
                                                console.error('PayPal Buttons failed to render')
                                            })
                                    } else {
                                        console.log('The funding source is ineligible')
                                    }
                                }
                            </script>
                            <!-- End of PayPal Checkout code -->


                            
                            <div class="text-xs text-gray-500 italic text-center">
                                <p class="mb-2">By clicking the button above, you agree to our <a href="#" class="text-blue-500">Terms of Service</a> and <a href="#" class="text-blue-500">Privacy Policy</a>.</p>
                             
                            </div>
                        </div>
                    </div>
                </div>
            </div> 
        </div>
    </section>

</body>
</html>