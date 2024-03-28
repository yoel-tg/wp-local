jQuery(document).ready(function ($) {
    $('#registration-review-form').on('submit', function (e) {
        e.preventDefault();
        const form = $(this)[0];
        const formData = $(this).serialize();
        $.ajax({
            method: 'POST',
            url: user_registration_review.ajaxurl,
            data: {
                action: 'urr_handle_user_registration_review',
                _wpnonce: user_registration_review.nonce,
                data: formData
            }, success: function (response) {
                var bgColor = 'green';
                // Handle success response
                if (response?.status === 'false') {
                    bgColor = 'red';
                } else {
                    form.reset();
                }
                Toastify({
                    text: response?.message, style: {
                        background: bgColor,
                    }
                }).showToast();
                urrLoadReviewDataOnPageLoad()

            }, error: function (xhr, status, error) {
                Toastify({
                    text: xhr.responseJSON?.message, style: {
                        background: "red",
                    }
                }).showToast();
            }
        });
    });

    $('#select-rating').on('change', function () {
        const val = this.value;
        urrLoadReviewDataOnPageLoad('rating');
    })

    $('.pagination-right').on('click', '.pagination-item', function () {
        $(".active").removeClass("active");
        $(this).addClass("active");
        urrLoadReviewDataOnPageLoad('pagination');
    })

    function urrPopulateReviews(reviews) {
        const reviewContainer = $('#review-table');
        reviewContainer.empty(); // Clear existing content
        let reviewTableHtml = '';

        if (reviewContainer.length > 0 && Object.keys(reviews).length > 0) {
            const userDataArray = Object.values(reviews);

            reviewTableHtml += `
        <table>
            <thead>
                <tr>
                    <th>User Login</th>
                    <th>User Email</th>
                    <th>Rating</th>
                    <th>Review</th>
                </tr>
            </thead>
            <tbody>
        `;

            userDataArray.forEach(function (review) {
                // Escape values before adding to HTML
                const userLogin = $('<div>').text(review.user_login).html();
                const userEmail = $('<div>').text(review.user_email).html();
                const rating = $('<div>').text(review.rating).html();
                const reviewText = $('<div>').text(review.review).html();

                reviewTableHtml += `
                    <tr>
                        <td>${userLogin}</td>
                        <td>${userEmail}</td>
                        <td>${rating}</td>
                        <td>${reviewText}</td>
                    </tr>`;
            });

            reviewTableHtml += `
            </tbody>
        </table>`;
        } else {
            reviewTableHtml = '<h4>No reviews available</h4>';
        }

        reviewContainer.html(reviewTableHtml);
    }

    function urrBuildQueryData(from) {
        var currentPage = (from == 'rating') ? 1 : $('.pagination-right .active').text();
        var selectedRating = $('#select-rating').val();

        return {
            'currentPage': currentPage,
            'selectedRating': selectedRating
        };
    }

    function urrLoadReviewDataOnPageLoad(from) {
        var data = urrBuildQueryData(from);

        $.ajax({
            method: 'GET',
            url: user_registration_review.ajaxurl,
            data: {
                action: 'urr_load_review_data',
                _wpnonce: user_registration_review.nonce,
                data: data
            },
            dataType: 'json',
            success: function (response) {
                urrPopulateReviews(response?.data?.reviews);
                if (from === 'rating') {
                    urrUpdateTableFooter(response?.data?.pagination_data, data.currentPage);
                }

            },
            error: function (xhr, status, error) {
                console.error('Error fetching review data:', error);
            }
        });
    }

    function urrUpdateTableFooter(paginationData, currentPage) {
        const paginationLeft = $('.pagination-left');
        const paginationRight = $('.pagination-right');
        paginationLeft.empty()
        paginationLeft.html(`<p>Total Entries: ${paginationData}</p>`)
        paginationRight.empty();
        paginationData = (paginationData < 5) ? 5 : paginationData
        let total = Math.ceil(paginationData / 5);
        console.log(paginationData, total, currentPage)
        let newHtml = '';
        for (let i = 1; i <= total; i++) {
            newHtml += `
            <span class="pagination-item ${i == currentPage ? 'active' : ''}">${i}</span>
            `
        }
        paginationRight.html(newHtml);

    }

});
