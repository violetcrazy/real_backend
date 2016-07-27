var tableAttr = false;
$(document).ready(function () {
    tableAttr = $('#table-list-attr').DataTable({
        "aoColumns":[
            {"bSortable": false},
            {"bSortable": false},
            {"bSortable": false},
            {"bSortable": false},
            {"bSortable": false},
        ],
        "aaSorting": [[0,'desc']],
        "ajax" : url,
        "pageLength": 25,
        "language": {
            "decimal": "",
            "emptyTable": "Không có thuộc tính",
            "infoEmpty":  "",
            "infoFiltered":   "(Tìm trong _MAX_ thuộc tính)",
            "infoPostFix": "",
            "thousands": ",",
            "loadingRecords": "Đang tải...",
            "processing": "Đang xử lý...",
            "search": "Tìm kiếm:",
            "lengthMenu": "Hiển thị _MENU_ thuộc tính trên 1 trang",
            "zeroRecords": "Không tìm thấy thuộc tính nào",
            "info": "Hiển thị _PAGE_/_PAGES_",
            "paginate": {
                "first": "«",
                "last": "»",
                "next": "›",
                "previous": "‹"
            },
            "aria": {
                "sortAscending":  ": activate to sort column ascending",
                "sortDescending": ": activate to sort column descending"
            }
        }
    });

    tableAttr.columns().every( function () {
        var that = this;
        $( 'input, select', this.footer() ).on( 'keyup change', function () {
            if ( that.search() !== this.value ) {
                that.search( this.value ).draw();
            }
        } );
    } );

    $('table').on('click', '.fancybox-run', function (event) {
        event.preventDefault();
        $this = $(this);
        $.fancybox({
            'width': '320',
            'height': 'auto',
            'autoScale': true,
            'transitionIn': 'fade',
            'transitionOut': 'fade',
            'type': 'iframe',
            'href': $this.attr('href')
        });
    });
    $('.btn-add-attr').on('click', function (event) {
        event.preventDefault();
        $this = $(this);
        $.fancybox({
            'width': '320',
            'height': 'auto',
            'autoScale': true,
            'transitionIn': 'fade',
            'transitionOut': 'fade',
            'type': 'iframe',
            'href': $this.attr('href')
        });
    });


});

function callBackOnSave(reload){
    if (reload == 1) {
        tableAttr.ajax.reload(false, false);
    } else {
        tableAttr.ajax.reload(false, true);
    }
}
