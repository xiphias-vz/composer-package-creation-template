'use strict';

const hiddenBucketFieldHandler = require('./set-hidden-bucket-field');
var filterField = $("input[name='filter-field']");
var downloadSelectedButtonPath = 'button[name="s3_download_delete_selected_form[s3-button-download]"]'
var deleteSelectedButtonPath = 'button[name="s3_download_delete_selected_form[s3-button-delete]"]'
var loadMoreButtonPath = 'button[name="load_more_data_form[LoadData]"]';
var tablePageLengthPath = '.dataTables_length select';
var searchAndFilterPath = 'input[type="search"]';
const storage= window.localStorage;
const mobileScreenMaxWidth = 475;
const paginationNumberLengthOnMobile = 5;
const s3TableUrl = '/s3-files-gui/index/s3-table';
function initialize() {

    seeLessPaginationOnMobileScreens();
    setTimeout(() => {
        let s3FilesTable = $('.table').DataTable();

        s3FilesTable.on('draw', function(e, settings){
            enableOrDisableFormButtons()
            addListenerToDeleteButtons()
            var processing = $('.dataTables_processing');
            if(processing.css('display') === 'block')
            {
                processing.css('display','none');
            }

            if(!settings.json.NextContinuationToken){
                disableElement(loadMoreButtonPath);
            }
            else{
                enableElement(loadMoreButtonPath);
            }

            $(".column-s3-bucket_checkbox input[type='checkbox']").on("change", toggleButtonsOnCheckboxSelect);

            $('#s3-bucket_name').on('click', function() {
                storage.setItem('tableAction', 'Sorting');
            });
        })

        s3FilesTable
            .on('preXhr.dt', function ( e, settings, data ) {
                disableElement(loadMoreButtonPath);
                data.action = 'Paging';

                if(storage.getItem('tableAction') === 'Sorting'){
                    data.action = 'Sorting';
                    storage.setItem('tableAction', '');
                }
            } );

        $(loadMoreButtonPath).on('click', function(event) {
            var processing = $('.dataTables_processing');
            var clickedButton = event.currentTarget;

            $.ajax({
                url: '/s3-files-gui/index/s3-table',
                method: 'POST',
                data: {
                    action: 'LoadMoreData'
                },
                beforeSend: function(){
                    processing.css('display','block');
                    $(clickedButton).prop('disabled', true);
                },
                success: function() {
                    s3FilesTable.draw(false);
                },
                complete: function(){
                    $(clickedButton).prop('disabled', false);
                }
            });
        });
        addListenerToFilterField(s3FilesTable);
        addListenerToFormDeleteMultipleButton();
        addListenerToClearFilterButton(s3FilesTable);
    }, 100);

    function addListenerToFilterField(s3FilesTable) {
        let timer;
        filterField.on('keyup', function() {
            clearTimeout(timer);
            var str = $(this).val();
            if (str.length > 2) {
                timer = setTimeout(function() {
                    let fullUrl = s3TableUrl + '?filterParam=' + filterField.val();
                    $.ajax({
                        url: fullUrl,
                        method: 'POST',
                        data: {
                            action: 'filter',
                            clearUsedFiltersFromSession: true
                        },
                        success: function() {
                            var currentPage = s3FilesTable.page();
                            s3FilesTable.page(currentPage).draw();
                        }
                    });
                }, 1000);
            }

            if (str.length === 0) {
                timer = setTimeout(function() {
                    $.ajax({
                        url: s3TableUrl,
                        method: 'POST',
                        data: {
                            action: 'clear-filter'
                        },
                        success: function() {
                            var currentPage = s3FilesTable.page();
                            s3FilesTable.page(currentPage).draw();
                        }
                    });
                }, 1000);
            }
        });
    }
    function addListenerToClearFilterButton(s3FilesTable) {
        $('.btn-clear-filter').on('click', () => {
            $.ajax({
                url: s3TableUrl,
                method: 'POST',
                data: {
                    action: 'clear-filter'
                },
                success: function() {
                    var currentPage = s3FilesTable.page();
                    s3FilesTable.page(currentPage).draw();
                    filterField.val(null);
                }
            });
        })
    }
    function addListenerToFormDeleteMultipleButton() {
        let multipleDeleteButton = $(deleteSelectedButtonPath);
        multipleDeleteButton.on('click', () => {
            $(".modal-delete-single-wrapper").hide();
            $(".modal-delete-multiple-wrapper").show();
      })
    }
    function enableOrDisableFormButtons() {
        disableElement(downloadSelectedButtonPath);
        disableElement(deleteSelectedButtonPath);
        disableElement(searchAndFilterPath);

        if (hiddenBucketFieldHandler.getS3BucketName()) {
            enableElement(searchAndFilterPath);
        }

        if (document.querySelector('td.dataTables_empty')) {
            disableElement(tablePageLengthPath);
            disableElement(loadMoreButtonPath);
        } else {
            enableElement(tablePageLengthPath);
            enableElement(loadMoreButtonPath);
        }
    }

    function enableElement(path) {
        $(path).removeClass('disabled');
        $(path).prop('disabled', false);
    }
    function disableElement(path) {
        $(path).addClass('disabled');
        $(path).prop('disabled', true);
    }
    function addListenerToDeleteButtons() {
        const deleteButtonNodeList = document.querySelectorAll('.btn-danger:not(button)');
        deleteButtonNodeList.forEach((deleteButton) => {
            deleteButton.addEventListener('click', (e) => {
                e.preventDefault();
                displayModal();
                let deleteActionUrl = deleteButton.getAttribute('href');
                setDeleteActionUrlToModalButton(deleteActionUrl);
            })
        })
    }
    function setDeleteActionUrlToModalButton(deleteActionUrl) {
        let modalButton = document.querySelector('.btn-modal-delete-single')
        modalButton.href = deleteActionUrl;
    }
    function displayModal() {
        $('.modal-delete-single-wrapper').show();
        $('.modal-delete-multiple-wrapper').hide();
        $('.confirm-delete-modal').modal('show');
    }

    function toggleButtonsOnCheckboxSelect() {
        if ($(".column-s3-bucket_checkbox input[type='checkbox']:checked").length > 0) {
            enableElement(downloadSelectedButtonPath);
            enableElement(deleteSelectedButtonPath);
        } else {
            disableElement(downloadSelectedButtonPath);
            disableElement(deleteSelectedButtonPath);
        }
    }

    function seeLessPaginationOnMobileScreens() {
        if (window.screen.width <= mobileScreenMaxWidth) {
            $.fn.DataTable.ext.pager.numbers_length = paginationNumberLengthOnMobile;
        }
    }
}

module.exports = {
    initialize: initialize,
};
