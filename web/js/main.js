function ordinal(min, max) {
    var numbers = [];
    for(var i = min; i <= max; i++) {
        numbers.push(getOrdinal(i));
    }

    return numbers;
};

function months() {
    return [ "January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December" ];
}

function getOrdinal(n) {
   var s=["th","st","nd","rd"],
       v=n%100;
   return n+(s[(v-20)%10]||s[v]||s[0]);
};

function buildIt(target, data) {
    var $target = $(target);

    $.each(data, function(paragraph_index, paragraph) {
        var $p = $('<p>');
        var autocompletes = {};

        $.each(paragraph, function(sentence_index, sentence) {
            var view = {};

            $.each(sentence.vars, function(name, options) {
                var id = "_" + paragraph_index + "_" + sentence_index + "_" + name;

                // TODO handle trailing punctuation
                view[name] = '<span class="input"><input type="text" id="' + id + '" name="' + name + '" /><span class="hint">' + (options.placeholder || name) + '</span></span>';

                if (options.autocomplete) {
                    autocompletes[id] = options.autocomplete;
                }
            });

            var inner = Mustache.render(sentence.text, view);
            var match = inner.match(/>(.)$/);
            if (match) {
                inner = inner.substring(0, inner.length - 1);
                $elm = $('<span>').append(inner).addClass('sentence');
                var test = $elm.find('.input').last().wrap($('<span class="nowrap">')).after(match[1]);
            } else {
                $elm = $('<span>').append(inner).addClass('sentence');
            }


            $p.append($elm);
        });

        $target.append($p);

        $.each(autocompletes, function(id, autocomplete) {
            $('#'+id).autocomplete({
                data: autocomplete,
                minChars: 1,
                matchInside: false
            });
        });
    });

    $('.madlibs').on('keydown keyup blur', '.sentence input', function(e) {
        var $input = $(this);
        var text = $input.val() == '' ? $input.attr('placeholder') : $input.val()
        var $junkspan = $('<span>').copyCSS($input).css({width: 'auto', display: 'inline-block'}).appendTo('body');
        var em = $junkspan.text('M').width() * 1.25;

        $junkspan.text(text);
        $input.width($junkspan.width() + em).css({paddingLeft: em + "px", paddingRight: 0});
        $junkspan.remove();
    }).find('input').keyup();
}

var joinflow = [
    [
        {
            text: "Hi, my name is {{{name}}},",
            vars: {
                name: {placeholder: 'Full name'}
            }
        }
    ],
    [
        {
            text: "I will celebrate my {{{age}}} birthday on the {{{day}}} of {{{month}}}.",
            vars: {
                age: {placeholder: 'Number', autocomplete: ordinal(1,120)},
                day: {autocomplete: ordinal(1,31)},
                month: {autocomplete: months()}
            }
        },
        {
            text: "It seems like just yesterday I was a {{{adj}}} {{{noun}}} new to {{{vocation}}}, but now I like to show {{{group}}} how to do it.",
            vars: {
                adj: {placeholder: 'Adjective'},
                noun: {},
                vocation: {},
                group: {}
            }
        },
        {
            text: "At parties I get introduced as the {{{gender}}} everybody goes to for advice on {{{topic}}}.",
            vars: {
                gender: {},
                topic: {}
            }
        },
        {
            text: "But it might surprise you to know I am also a pretty good {{{noun1}}}, and I’d even say I am an expert {{{noun2}}}.",
            vars: {
                noun1: {},
                noun2: {}
            }
        },
        {
            text: "There are so many things I want to learn to do better! If I had to pick just three I would want to {{{thing1}}}, {{{thing2}}}, and {{{thing3}}} more wisely. But I would definitely want to learn from someone {{{adj}}} who {{{verb}}} {{{adverb}}}.",
            vars: {
                thing1: {},
                thing2: {},
                thing3: {},
                adj: {},
                verb: {},
                adverb: {}
            }
        },
        {
            text: "Someday maybe I’ll leave {{{currentlocation}}} behind and become a {{{newjob}}} in {{{futurelocation}}}",
            vars: {
                currentlocation: {},
                newjob: {},
                futurelocation: {}
            }
        },
        {
            text: "I've checked out some great coffee shops around {{{favoriteneighborhood}}}.",
            vars: {
                favoriteneighborhood: {}
            }
        },
        {
            text: "I prefer {{{communicationmethod}}} and I could also {{{communicationmethod2}}}.",
            vars: {
                communicationmethod: {},
                communicationmethod2: {}
            }
        },
        {
            text: "If that works out we could {{{connectiontype}}}.",
            vars: {
                connectiontype: {}
            }
        }
    ]
];

buildIt('#madlibs_test', joinflow);


