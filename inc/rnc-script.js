jQuery(document).ready(function($) {
    $('.tcode').textillate({
        loop: true,
        minDisplayTime: 5000,
        initialDelay: 800,
        autoStart: true,
        inEffects: [],
        outEffects: [],
        in: {
            effect: 'rollIn',
            delayScale: 1.5,
            delay: 50,
            sync: false,
            shuffle: true,
            reverse: false,
            callback: function() {}
        },
        out: {
            effect: 'fadeOut',
            delayScale: 1.5,
            delay: 50,
            sync: false,
            shuffle: true,
            reverse: false,
            callback: function() {}
        },
        callback: function() {}
    });
})

