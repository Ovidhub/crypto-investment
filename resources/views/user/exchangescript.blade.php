<script>
    // Get DOM elements
    let destinationasset = document.getElementById('destinationasset');
    let sourceasset = document.getElementById('sourceasset');
    let amount = document.getElementById('amount');
    let quatity = document.getElementById('quantity');
    let realquantity = document.getElementById('realquantity');
    
    // Add event listeners for asset selection changes
    destinationasset.addEventListener('change', validate);
    sourceasset.addEventListener('change', validate);
    
    // Initial validation on page load
    if (destinationasset.value == sourceasset.value) {
        window.notify({
            title: 'Error',
            message: 'Source and Destination account cannot be the same',
        },{
            type: 'danger',
        });
                
        destinationasset.value = '';
        amount.placeholder = '';
        quatity.placeholder = '';
        amount.value = '';
        quatity.value = '';
    } else {
        amount.placeholder = `Enter amount of ${sourceasset.value}`;
        quatity.placeholder = `Quantity of ${destinationasset.value}`;
    }
    
    // Validation function for account selection
    function validate(){
        // Reset values when changing assets
        amount.value = '';
        quatity.value = '';
        realquantity.value = '';
        
        // Add visual feedback for selection change
        amount.classList.add('border-primary');
        setTimeout(() => {
            amount.classList.remove('border-primary');
        }, 1000);
        
        // Check if source and destination are the same
        if (destinationasset.value == sourceasset.value) {
            window.notify({
                title: 'Error',
                message: 'Source and Destination account cannot be the same',
            },{
                type: 'danger',
            });
                  
            destinationasset.value = '';
            amount.placeholder = '';
            quatity.placeholder = '';
            amount.value = '';
            quatity.value = '';
        } else {
            // Update placeholders with selected assets
            amount.placeholder = `Enter amount of ${sourceasset.value}`;
            quatity.placeholder = `Quantity of ${destinationasset.value}`;
            
            // Update currency displays if elements exist
            const sourceCurrency = document.getElementById('source-currency');
            const destCurrency = document.getElementById('dest-currency');
            
            if (sourceCurrency) {
                sourceCurrency.textContent = sourceasset.value.toUpperCase();
            }
            
            if (destCurrency) {
                destCurrency.textContent = destinationasset.value.toUpperCase();
            }
        }
    }

    // Add event listener for amount changes
    amount.addEventListener('keyup', getQuantity);
    amount.addEventListener('input', getQuantity);

    // Function to get exchange quantity based on amount
    function getQuantity(){
        // Only proceed if amount has a value
        if (amount.value && amount.value > 0) {
            // Show loading state
            quatity.value = 'Calculating...';
            
            // Build the API URL
            let uurl = "{{url('/dashboard/asset-price/')}}" + '/' + sourceasset.value + '/' + destinationasset.value + '/' + amount.value;
            
            // Fetch exchange rate data
            fetch(uurl)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(response => {
                    if (response.status === 200) {
                        // Format and display the quantity
                        quatity.value = response.data + ' ' + destinationasset.value;
                        realquantity.value = response.data;
                        
                        // Add visual feedback for successful rate fetch
                        quatity.classList.add('pulse-green');
                        setTimeout(() => {
                            quatity.classList.remove('pulse-green');
                        }, 2000);
                    } else {
                        // Handle API error response
                        quatity.value = 'Error calculating rate';
                        console.error('API Error:', response);
                    }
                })
                .catch(error => {
                    // Handle network or other errors
                    console.error('Exchange Rate Error:', error);
                    quatity.value = 'Could not calculate rate';
                });
        } else {
            // Clear output if amount is empty
            quatity.value = '';
            realquantity.value = '';
        }
    }

    // Set up the form submission
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('exchnageform');
        
        if (form) {
            form.addEventListener('submit', function(event) {
                event.preventDefault();
                exchangeNow(event);
                return false;
            });
        }
        
        // Initialize asset currency displays
        const sourceCurrency = document.getElementById('source-currency');
        const destCurrency = document.getElementById('dest-currency');
        
        if (sourceCurrency && sourceasset) {
            sourceCurrency.textContent = sourceasset.value.toUpperCase();
        }
        
        if (destCurrency && destinationasset) {
            destCurrency.textContent = destinationasset.value.toUpperCase();
        }
    });

    // Function to handle the exchange submission
    function exchangeNow(event) {
        event.preventDefault();
        
        // Validate amount is provided
        if (!amount.value || amount.value <= 0) {
            window.notify({
                title: 'Error',
                message: 'Please Enter a valid Amount to Exchange',
            },{
                type: 'danger',
            });
            
            // Add visual feedback for error
            amount.classList.add('border-red-500');
            amount.focus();
            setTimeout(() => {
                amount.classList.remove('border-red-500');
            }, 2000);
            
            return false;
        } else {
            // Show loading state
            const submitBtn = document.querySelector('#exchnageform button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Processing...';
            submitBtn.disabled = true;
            submitBtn.classList.add('opacity-75');
            
            // Get form data
            const formData = new FormData(document.getElementById('exchnageform'));
            
            // Create request headers
            const headers = {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            };
            
            // Send the request
            fetch("{{route('exchangenow')}}", {
                method: 'POST',
                headers: headers,
                body: formData,
                credentials: 'same-origin'
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(response => {
                // Reset button state
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
                submitBtn.classList.remove('opacity-75');
                
                if (response.status === 200) {
                    // Show success notification
                    window.notify({
                        title: 'Success',
                        message: response.success,
                    },{
                        type: 'success',
                    });
                    
                    // Add visual feedback for success
                    const formContainer = document.querySelector('#exchnageform').closest('.bg-white');
                    formContainer.classList.add('pulse-green');
                    
                    // Reset form fields
                    amount.value = '';
                    quatity.value = '';
                    realquantity.value = '';
                    
                    // Refresh balances immediately
                    getcurrbalance();
                    
                    // Reload after delay for complete refresh
                    setTimeout(function(){ 
                        window.location.reload(true);
                    }, 3000);
                } else {
                    // Show error notification
                    window.notify({
                        title: 'Error',
                        message: response.message || 'Something went wrong with your exchange',
                    },{
                        type: 'danger',
                    });
                }
            })
            .catch(error => {
                // Reset button state
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
                submitBtn.classList.remove('opacity-75');
                
                console.error('Exchange Error:', error);
                
                // Show error notification
                window.notify({
                    title: 'Error',
                    message: 'An error occurred while processing your request. Please try again.',
                },{
                    type: 'danger',
                });
            });
        }
    }

    // Function to get and update cryptocurrency balances
    function getcurrbalance() {
        let usdelement = document.querySelectorAll('.usdelement');
        
        usdelement.forEach(element => {
            // Get the cryptocurrency from the element ID
            let coin = element.id;
            
            // Add loading state
            element.innerHTML = '<span class="inline-block w-full h-4 bg-light-100 dark:bg-dark-300 rounded shimmer"></span>';
            
            // Fetch updated balance
            fetch("{{url('dashboard/balances/')}}" + '/' + coin)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Failed to fetch balance');
                    }
                    return response.json();
                })
                .then(response => {
                    if (response.data) {
                        // Update balance with currency symbol
                        element.textContent = "{{$settings->currency}}" + response.data;
                        
                        // Add subtle animation for balance updates
                        element.classList.add('pulse-green');
                        setTimeout(() => {
                            element.classList.remove('pulse-green');
                        }, 2000);
                    } else {
                        element.textContent = "Error loading balance";
                    }
                })
                .catch(error => {
                    console.error('Balance Fetch Error:', error);
                    element.textContent = "Error loading balance";
                });
        });
    }
    
    // Initial balance load
    getcurrbalance();
    
    // Setup auto-refresh for balances
    setInterval(function(){
        getcurrbalance();
    }, 60000); // Every 60 seconds
</script>