$(document).ready(function () {
    const addItemToCart = (productId, successMessage, errorMessage) => {
        $.ajax({
            url: '/cart',
            data: {productId},
            method: 'POST',
            success: function (response) {
                $.toast({
                    heading: successMessage,
                    showHideTransition: 'slide',
                    icon: 'success'
                })
            },
            error: function (xhr, status, error) {
                $.toast({
                    heading: errorMessage,
                    showHideTransition: 'fade',
                    icon: 'error'
                })
            }
        });
    }

    const removeItemFromCart = async (productId, successMessage, errorMessage) => {
        const errorToast = () => {
            $.toast({
                heading: errorMessage,
                showHideTransition: 'fade',
                icon: 'error'
            });
        };

        try {
            const response = await fetch(`/cart/${productId}`, {
                method: 'PATCH',
            });


            if (response.ok) {
                $.toast({
                    heading: successMessage,
                    showHideTransition: 'slide',
                    icon: 'success'
                });
                const data = await response.json();
                console.log('response:', data);
            } else {
                errorToast();
            }
        } catch (error) {
            errorToast();
        }
    };
    $('.add-to-cart-btn').click(function () {
        let productId = $(this).data('product-id');
        addItemToCart(productId, 'Product added to cart successfully.', 'Adding product to cart failed.');
    });
    $('.remove-item').click(function (event) {
        event.preventDefault();
        let productId = $(this).data('product-id');
        removeItemFromCart(
            productId,
            'Product removed from cart successfully.',
            'Removing product from cart failed.'
        ).then(() => window.location.reload());
    });

    const stepButtons = document.querySelectorAll('.step-button');
    const progress = document.querySelector('#progress');

    Array.from(stepButtons).forEach((button,index) => {
        button.addEventListener('click', () => {
            progress.setAttribute('value', index * 100 /(stepButtons.length - 1) );

            stepButtons.forEach((item, secindex)=>{
                if(index > secindex){
                    item.classList.add('done');
                }
                if(index < secindex){
                    item.classList.remove('done');
                }
            })
        })
    })
});
