@extends('layout.main')
@section('content')


    <div class="container ">



        <div class="col-lg-12 py-5 col-sm-12 d-flex justify-content-center">
            <div class="card border-0 shadow-lg mb-5 bg-body rounded-20">
                <div class="card-body">

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    @if (session()->has('message'))
                        <div class="alert alert-success">
                            {{ session()->get('message') }}
                        </div>
                    @endif
                    @if (session()->has('error'))
                        <div class="alert alert-danger">
                            {{ session()->get('error') }}
                        </div>
                    @endif


                    <div class="card">




                        <div class="card-body">
                            <label>📞 Number </label>
                            <div class="input-group">
                                <input type="text" id="copyTarget" class="form-control" value="{{ $sms_order->phone }}">
                                <span id="copyButton" class="input-group-addon btn" title="Click to copy">
                                    <i class="fa fa-clipboard" aria-hidden="true"></i>
                                </span>
                            </div>


                            <label class="my-1">💬 Code from SMS </label>
                            <div class="input-group">
                                <input type="text" readonly id="response-input" class="form-control">
                                <span id="copyButton2" class="input-group-addon btn" title="Click to copy">
                                    <i class="fa fa-clipboard" aria-hidden="true"></i>
                                </span>
                            </div>


                            {{-- <div id="timer">Countdown Timer: <span id="countdown"></span></div>
                            <div id="progress-bar">
                                <div id="progress"></div>
                            </div> --}}


                            <div class="progress my-2">
                                <div id="progress-bar">
                                    <div id="progress"></div>
                                </div>
                            </div>
                            <div class="text-small text-center text-danger" id="timer">Order expires in: <span
                                    id="countdown"></span></div>


                            <div class="row d-flex justify-content-center my-2">

                                <a style="font-size: 10px; border:0px; background:red"
                                    class="me-2 col btn btn-warning btn-sm btn-block"
                                    href="cancle-sms?id={{ $sms_order->id }}" role="button"><i class=""> Delete
                                        Order</a></i>

                                {{-- <a style="font-size: 10px" class="col text-white btn btn-success btn-sm btn-block"
                                    href="check-sms?id={{ $sms_order->id}}" role="button"><i
                                        class="bi bi-arrow-clockwise"> Recheck
                                        Order</a></i> --}}

                            </div>











                        </div>

                    </div>


                </div>


            </div>
        </div>

        <script src="/livewire/livewire.js?id=90730a3b0e7144480175" data-turbo-eval="false" data-turbolinks-eval="false">
        </script>
        <script data-turbo-eval="false" data-turbolinks-eval="false">
            window.livewire = new Livewire();
            window.Livewire = window.livewire;
            window.livewire_app_url = '';
            window.livewire_token = 'JBt4aOzGju0YuBweWShPMRkAkmVxvzZzG4XOMx7V';
            window.deferLoadingAlpine = function(callback) {
                window.addEventListener('livewire:load', function() {
                    callback();
                });
            };
            let started = false;
            window.addEventListener('alpine:initializing', function() {
                if (!started) {
                    window.livewire.start();
                    started = true;
                }
            });
            document.addEventListener("DOMContentLoaded", function() {
                if (!started) {
                    window.livewire.start();
                    started = true;
                }
            });
        </script>


        <script>
            const countdownDuration = 420;

            const countdownElement = document.getElementById('countdown');
            const progressElement = document.getElementById('progress');

            const widthChangePerSecond = 100 / countdownDuration;

            let countdown = countdownDuration;
            countdownElement.textContent = formatTime(countdown);

            const timerInterval = setInterval(() => {
                countdown--;
                countdownElement.textContent = formatTime(countdown);

                const progressBarWidth = widthChangePerSecond * (countdownDuration - countdown);
                progressElement.style.width = `${progressBarWidth}%`;

                if (countdown <= 0) {
                    clearInterval(timerInterval);
                    window.location.href = '{{ url('') }}/check-sms?id={{ $sms_order->id}}'; // Replace with your desired URL
                }
            }, 1000);

            function formatTime(seconds) {
                const minutes = Math.floor(seconds / 60);
                const remainingSeconds = seconds % 60;
                return `${minutes}:${remainingSeconds < 10 ? '0' : ''}${remainingSeconds}`;
            }
        </script>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const table = document.getElementById('data-table');
                const rows = table.querySelectorAll('tbody tr');

                rows.forEach(row => {
                    const countdownElement = row.cells[2]; // Assumes "Expires" is in the third column (index 2)
                    let seconds = parseInt(countdownElement.getAttribute('data-seconds'), 10);

                    const countdownInterval = setInterval(function() {
                        countdownElement.textContent = seconds + 's';

                        if (seconds <= 0) {
                            clearInterval(countdownInterval);
                            // Add your logic to handle the expiration, e.g., sendPostRequest(row);
                            console.log('Expired:', row);
                        }

                        seconds--;
                    }, 1000);
                });

                // You may add the sendPostRequest function here or modify the code accordingly
            });
        </script>

        <script>
            function copyToClipboard(element) {
                var $temp = $("<input>");
                $("body").append($temp);
                $temp.val($(element).text()).select();
                document.execCommand("copy");
                $temp.remove();
            }

            var addrsField = $('.input_copy .txt');
            @if ($order == 1)
                addrsField.text("{{ "$sms_order->phone" ?? null }} ");
            @endif
            $('.input_copy .icon').click(function() {
                copyToClipboard('.input_copy .txt');
                addrsField.addClass('flashBG')
                    .delay('1000').queue(function() {
                        addrsField.removeClass('flashBG').dequeue();
                    });
            });





            var addrsField = $('.input_copy2 .txt2');
            @if ($order == 1)
                addrsField.text("{{ "$sms_order->sms" ?? null }} ");
            @endif
            $('.input_copy2 .icon2').click(function() {
                copyToClipboard('.input_copy2 .txt2');
                addrsField.addClass('flashBG')
                    .delay('1000').queue(function() {
                        addrsField.removeClass('flashBG').dequeue();
                    });
            });


            (function() {
                "use strict";

                function copyToClipboard(elem) {
                    var target = elem;

                    // select the content
                    var currentFocus = document.activeElement;

                    target.focus();
                    target.setSelectionRange(0, target.value.length);

                    // copy the selection
                    var succeed;

                    try {
                        succeed = document.execCommand("copy");
                    } catch (e) {
                        console.warn(e);

                        succeed = false;
                    }

                    // Restore original focus
                    if (currentFocus && typeof currentFocus.focus === "function") {
                        currentFocus.focus();
                    }

                    if (succeed) {
                        $(".copied").animate({
                            top: -25,
                            opacity: 0
                        }, 700, function() {
                            $(this).css({
                                top: 0,
                                opacity: 1
                            });
                        });
                    }

                    return succeed;
                }

                $("#copyButton, #copyTarget").on("click", function() {
                    copyToClipboard(document.getElementById("copyTarget"));
                });
            })();







            (function() {
                "use strict";

                function copyToClipboard(elem) {
                    var target = elem;

                    // select the content
                    var currentFocus = document.activeElement;

                    target.focus();
                    target.setSelectionRange(0, target.value.length);

                    // copy the selection
                    var succeed;

                    try {
                        succeed = document.execCommand("copy");
                    } catch (e) {
                        console.warn(e);

                        succeed = false;
                    }

                    // Restore original focus
                    if (currentFocus && typeof currentFocus.focus === "function") {
                        currentFocus.focus();
                    }

                    if (succeed) {
                        $(".copied").animate({
                            top: -25,
                            opacity: 0
                        }, 700, function() {
                            $(this).css({
                                top: 0,
                                opacity: 1
                            });
                        });
                    }

                    return succeed;
                }



                $("#copyButton2, #copyTarget2").on("click", function() {
                    copyToClipboard(document.getElementById("copyTarget2"));
                });
            })();




            makeRequest();
            setInterval(makeRequest, 5000);

            function makeRequest() {
                fetch('{{ url('') }}/get-smscode?num={{ $sms_order->phone }}')
                    .then(response => {
                        if (!response.ok) {
                            throw new Error(`HTTP error! Status: ${response.status}`);
                        }
                        return response.json();
                    })
                    .then(data => {

                        console.log(data.message);
                        displayResponse(data.message);

                    })
                    .catch(error => {
                        console.error('Error:', error);
                        displayResponse({
                            error: 'An error occurred while fetching the data.'
                        });
                    });
            }

            function displayResponse(data) {
                const responseInput = document.getElementById('response-input');
                responseInput.value = data;
            }


            // function reloadPage() {
            //     location.reload();
            // }

            // setInterval(reloadPage, 40000);
        </script>

    @endsection
