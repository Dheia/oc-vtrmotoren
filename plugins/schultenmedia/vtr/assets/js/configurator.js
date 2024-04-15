
$(function() {
    console.log( "ready!" );

    var currentStep = 1
    oc.request('#configurator', 'onUpdate')

    $(document).on('click', '.configurator-property button', function(e) {

        e.preventDefault();

        var $this = $(this);
        $this.parent().toggleClass('active')

        return false;

    });

    $(document).on('mouseenter', '[data-configurator-preview]', function() {

        var $this = $(this);

        $('.preview-image').attr('src', $this.attr('data-configurator-preview'))
        $('.preview-image').removeClass('hidden')


    }).on('mouseleave', function() {
        $('.preview-image').addClass('hidden')
    });

    $(document).on('click', '.property-option-input', function() {

        var $this = $(this);
        var property = $this.closest('.configurator-property');
        var option = $this.closest('[data-configurator-option]');

        $this.find('input').trigger('click');

        property.find('[data-configurator-option].selected').removeClass('selected');
        property.removeClass('active')
        option.addClass('selected');

        // Get next property, add active
        property.next().addClass('active')

        // console.log($this, option.attr('data-configurator-option'), property.attr('data-configurator-property'));
        oc.request('#configurator', 'onUpdate')

    });


    $(document).on('click', '[data-configurator-next]', function(e) {
        currentStep++;
        $("html, body").animate({ scrollTop: 0 }, 200);
        $('[data-configurator-step]').each(function() {
            var $this = $(this);
            $this.addClass('hidden');
            if($this.attr('data-configurator-step') == currentStep) {
                $this.removeClass('hidden');
            }
        });
        e.preventDefault()
    });

    $(document).on('click', '[data-configurator-previous]', function(e) {
        currentStep--;
        $("html, body").animate({ scrollTop: 0 }, 200);
        $('[data-configurator-step]').each(function() {
            var $this = $(this);
            $this.addClass('hidden');
            if($this.attr('data-configurator-step') == currentStep) {
                $this.removeClass('hidden');
            }
        });
        e.preventDefault()
    });

});

function toggleConfigurator() {

    $('.configurator-modal').toggleClass('hidden')
    $('body').toggleClass('configurator-open')

}
