var order = {
    init: function(){
        order.collection = $('.order');
        order.collection.data('index', order.collection.find(':input').length);
        order.lastRow = $('.order-last')
        order.newItemLink = $('.order-add-item');
        order.observer();
        order.calculateTotal();
    },
    observer: function(){
        order.newItemLink.click(function(e){
            e.preventDefault();
            order.addItem();
        })

        $('.autocomplete-hidden').each(function(){
            order.observeExtraInfo($(this));
        })

        $('.order-quantity').each(function(){
            order.observeSubtotal($(this));
        })

        $('.order-row').each(function(){
            order.observeRemoveItem($(this));
        })
    },
    addItem: function(){
        var proto = order.collection.data('prototype');
        proto = proto.replace(/__name__/g, order.collection.data('index') + 1);
        order.collection.data('index', order.collection.data('index') + 1);
        var $proto = $(proto);
        var insert = order.lastRow.before($proto).promise();

        insert.done(function(){
            sl_resources.autocompleter.reinit();

            var $inputProduct = $proto.find('.autocomplete-hidden');
            var $inputQuantity = $proto.find('.order-quantity');

            order.observeExtraInfo($inputProduct);
            order.observeSubtotal($inputQuantity);
            order.observeRemoveItem($proto);
        })
    },
    observeRemoveItem: function($container){
        $container.find('.order-remove-item').click(function(e){
            e.preventDefault();
            $container.remove();
            order.calculateTotal();
        })
    },
    observeExtraInfo: function($input){
        if ($input.length > 0){
            $input.change(function(e){
                var id = $(this).val();
                $tr = $(this).parents('tr').first();
                $.ajax({
                    url : order.collection.data('url-info'),
                    data : { id: id, attributes: order.collection.data('extra-fields').split(',')},
                    dataType: 'json',
                    type: 'POST',
                    success: function(json){
                        for (var attr in json){
                            if (json.hasOwnProperty(attr)) {
                                $tr.find('.order-' + attr).html(json[attr]);
                                $tr.find('.order-' + attr).data('value', json[attr]);
                            }
                            order.updateSubtotal($tr);
                        }
                    }
                })
            })
        }
    },
    observeSubtotal: function($input){
        $input.change(function(e){
            var $tr = $(this).parents('tr').first();
            order.updateSubtotal($tr);
        })
    },
    updateSubtotal: function($container){
        var subtotal = order.caclulateSubtotal($container);
        $container.find('.order-subtotal').html(subtotal);
        order.calculateTotal();
    },
    caclulateSubtotal: function($container){
        var price = $container.find('.order-price').data('value');
        var quantity = $container.find('.order-quantity').val();

        price = Number(price) || 0;
        quantity = Number(quantity) || 0;

        var subtotal = price * quantity;
        return subtotal;
    },
    calculateTotal: function(){
        var total = 0;
        $('.order-row').each(function(){
            total += order.caclulateSubtotal($(this));
        })
        $('.order-total').html(total);
    }
}

order.init();