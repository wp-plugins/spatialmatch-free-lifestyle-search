if (window.SpatialMatch == null) { SpatialMatch = { } }

SpatialMatch.Map =
{
    embed: function (config)
    {
        config = (config != null) ? config : { };

        var flashvars = { };

        var renderTo = null;

        var width = '600px';
        var height = '500px';

        for (var prop in config)
        {
            if (prop == 'licenseKey')
            {
                flashvars.smKey = jQuery.trim(config[prop]);
            }
            else if (prop == 'width')
            {
                width = config[prop];

                if (SpatialMatch.String.isNumber(width))
                {
                    width = width + 'px';
                }
            }
            else if (prop == 'height')
            {
                height = config[prop];

                if (SpatialMatch.String.isNumber(height))
                {
                    height = height + 'px';
                }
            }
            else if (prop == 'bookmark')
            {
                var tokens = config[prop].split(';');

                for (var ii = 0; ii < tokens.length; ii++)
                {
                    var parts = tokens[ii].split('=');

                    var key = jQuery.trim(parts[0]);
                    var value = jQuery.trim(parts[1]);

                    if ((key != null) && (key.length > 0) && (value != null) && (value.length > 0))
                    {
                        flashvars[key] = value;
                    }
                }
            }
            else if (prop == 'renderTo')
            {
                renderTo = jQuery.trim(config[prop]);
            }
            else
            {
                flashvars[prop] = config[prop];
            }
        }

        var uid = SpatialMatch.Util.uid();

        var id = 'spatialMatchMap-obj-' + uid;

        var parameters =
        {
            bgcolor:           '#4B4B4B',
            allowFullScreen:   true,
            allowScriptAccess: 'always',
            wmode:             'opaque'
        };

        var attributes =
        {
            id: id,
            name: id
        };

        if ((renderTo == null) || (renderTo.length == 0))
        {
            renderTo = 'spatialMatchMap-div-' + uid;

            document.write('<div id="' + renderTo + '" style="width:' + width + ";height:" + height + '">');
            document.write('This page requires <a href="http://activatejavascript.org/">JavaScript</a> and the ');
            document.write('<a href="http://www.adobe.com/go/getflashplayer">Adobe Flash Player</a>.');
            document.write('</div>');
        }

        swfobject.embedSWF('//app.spatialmatch.com/SpatialMatch.swf',
                           renderTo,
                           width,
                           height,
                           '9.0.45',
                           '//app.spatialmatch.com/third-party/swfobject/expressInstall.swf',
                           flashvars,
                           parameters,
                           attributes);

        if (SpatialMatch.AdWords)
        {
            SpatialMatch.Event.addListener(id, 'userRegistration', SpatialMatch.AdWords.track);
        }

        return id;
    },

    popup: function (id, width, height, config)
    {
        width = (width != null) ? width : jQuery(window).width() - 50;
        height = (height != null) ? height : jQuery(window).height() - 50;

        var div = jQuery('#' + id);

        if (div != null)
        {
            div.dialog(
            {
                width: width,
                height: height,
                modal: true,
                resizable: false,
                closeOnEscape: false,

                create: function ()
                {
                    config['sm.popup'] = true;

                    var mapId = SpatialMatch.Map.embed(config);

                    SpatialMatch.Event.addListener(mapId, 'keyboardEscape', function ()
                    {
                        div.dialog('close');
                    });
                }
            });
        }
    }
}

SpatialMatch.Util =
{
    uid: function ()
    {
        if (SpatialMatch.Util.__uid == null)
        {
            SpatialMatch.Util.__uid = Math.floor(Math.random() * 100);
        }

        return SpatialMatch.Util.__uid++;
    },

    getParameter: function (name)
    {
        name = name.replace(/[\[]/, '\\\[').replace(/[\]]/, '\\\]');

        var regex = new RegExp('[\\?&]' + name + '=([^&#]*)');

        var results = regex.exec(window.location.search);

        return (results) ? decodeURIComponent(results[1].replace(/\+/g, ' ')) : null;
    }
}

SpatialMatch.String =
{
    isNumber: function (value)
    {
        if (SpatialMatch.String.__numberRegEx == null)
        {
            SpatialMatch.String.__numberRegEx = /^\d+$/;
        }

        return SpatialMatch.String.__numberRegEx.test(value);
    },

    commatize: function (s)
    {
        s = (s != null) ? jQuery.trim(s) : '';

        var parts = s.split('.');

        var whole = parts[0];
        var fractional = (parts.length > 1) ? '.' + parts[1] : '';

        if (SpatialMatch.String.__wholeRegEx == null)
        {
            SpatialMatch.String.__wholeRegEx = /(\d+)(\d{3})/;
        }

        while (SpatialMatch.String.__wholeRegEx.test(whole))
        {
            whole = whole.replace(SpatialMatch.String.__wholeRegEx, '$1' + ',' + '$2');
        }

        return whole + fractional;
    },

    join: function (obj, separator)
    {
        var s = '';

        for (var key in obj)
        {
            if (s.length > 0)
            {
                s += separator;
            }

            s += (key + '=' + obj[key]);
        }

        return s;
    },

    toBoolean: function (value)
    {
        if ((value === true) || (value === false))
        {
            return value;
        }

        if ((value == 'true') || (value == 'yes') || (value == 'on'))
        {
            return true;
        }

        var iVal = parseInt(value);

        if ((iVal != 0) && !isNaN(iVal))
        {
            return true;
        }

        return false;
    }
}

SpatialMatch.SearchUtils =
{
    popup: function (widget, license, bookmark, options)
    {
        var uid = jQuery.trim(widget.attr('uid'));

        var container = jQuery('#' + uid);

        if (container != null)
        {
            var target = uid + '-target';

            container.empty();
            container.append('<div id="' + target + '"></div>')

            var config =
            {
                licenseKey: license,
                width: '100%',
                height: '100%',
                renderTo: target,
                bookmark: bookmark
            };

            SpatialMatch.Map.embed(config);

            var width = ((options != null) && (options.width != null)) ? options.width : jQuery(window).width() - 50;
            var height = ((options != null) && (options.height != null)) ? options.height : jQuery(window).height() - 50;

            container.dialog(
            {
                width: width,
                height: height,
                modal: true,
                resizable: false
            });
        }
    },

    page: function (widget, bookmark, options)
    {
        var url = ((options != null) && (options.url != null)) ? jQuery.trim(options.url) : '';

        if ((url != null) && (url.length > 0))
        {
            url += ((url.indexOf('?') == -1) ? '?' : '&') + bookmark;

            document.location = url;
        }
    }
}

SpatialMatch.PropertySearch =
{
    Formatters:
    {
        basic: function (field, low, high)
        {
            var min = (field.min != null) ? field.min : Number.MIN_VALUE;
            var max = (field.max != null) ? field.max : Number.MAX_VALUE;

            var prefix = (field.prefix != null) ? field.prefix : '';
            var suffix = (field.suffix != null) ? field.suffix : '';
            var units = (field.units != null) ? field.units : '';

            var lValue = SpatialMatch.PropertySearch.valueOf(field, low);
            var hValue = SpatialMatch.PropertySearch.valueOf(field, high);

            var lowString = (field.commatize !== false) ? SpatialMatch.String.commatize(lValue) : lValue;
            var highString = (field.commatize !== false) ? SpatialMatch.String.commatize(hValue) : hValue;

            if ((low == min) || (high == max))
            {
                if ((low == min) && (high == max))
                {
                    return (field.any != null) ? field.any : 'Any';
                }

                if (low == min)
                {
                    return prefix + highString + suffix + units + ((field.orless != null) ? field.orless : ' or less');
                }

                if (high == max)
                {
                    return prefix + lowString + suffix + units + ((field.ormore != null) ? field.ormore : ' or more');
                }
            }

            return prefix + lowString + suffix + ' - ' + prefix + highString + suffix + units;
        }
    },

    Fields:
    {
        baths:
        {
            label: 'Baths',
            type: 'range',
            min: 1,
            max: 5
        },

        beds:
        {
            label: 'Bedrooms',
            type: 'range',
            min: 1,
            max: 6
        },

        homeSize:
        {
            label: 'Home Size',
            type: 'range',
            min: 0,
            max: 11,
            units: ' sq ft.',
            any: 'Any Size',
            mappings: [ 1000, 1500, 2000, 2500, 3000, 3500, 4000, 4500, 5000, 7500, 10000, 15000 ]
        },

        keywords:
        {
            label: 'Keywords',
            type: 'text',
            automation: 'app:homes:filter.keywords'
        },

        landPrice:
        {
            label: 'Price',
            type: 'range',
            min: 0,
            max: 9,
            prefix: '$',
            any: 'Any Price',
            mappings: [ 10000,  25000, 50000, 75000, 100000, 125000, 150000, 175000, 200000, 250000 ]
        },

        leasePrice:
        {
            label: 'Price',
            type: 'range',
            min: 0,
            max: 23,
            prefix: '$',
            any: 'Any Price',
            mappings: [ 500, 600, 700, 800, 900, 1000,
                        1100, 1200, 1300, 1400, 1500,
                        1600, 1700, 1800, 1900, 2000,
                        2250, 2500, 2750, 3000,
                        3500, 4000, 4500, 5000 ]
        },

        listingPrice:
        {
            label: 'Price',
            type: 'range',
            min: 0,
            max: 55,
            prefix: '$',
            any: 'Any Price',
            mappings:
            [
                10000,    25000,    50000,    75000,
                100000,   125000,   150000,   175000,
                200000,   225000,   250000,   275000,
                300000,   325000,   350000,   375000,
                400000,   425000,   450000,   475000,
                500000,   525000,   550000,   575000,
                600000,   625000,   650000,   675000,
                700000,   725000,   750000,   775000,
                800000,   825000,   850000,   875000,
                900000,   925000,   950000,   975000,
               1000000,  1500000,  2000000,  2500000,
               3000000,  3500000,  4000000,  4500000,
               5000000,  5500000,  6000000,  6500000,
               7000000,  8000000,  9000000, 10000000
            ]
        },

        location:
        {
            label: 'Location',
            type: 'text',
            automation: 'map.goto',
            required: true
        },

        lotSize:
        {
            label: 'Lot Size',
            type: 'range',
            min: 0,
            max: 10,
            units: ' acres',
            any: 'Any Size',
            mappings: [ 0.10, 0.125, 0.25, 0.50, 0.75, 1, 2, 3, 4, 5, 10 ]
        },

        numUnits:
        {
            label: 'Number of Units',
            type: 'range',
            min: 1,
            max: 10,
            any: 'Any',
            commatize: false
        },

        yearBuilt:
        {
            label: 'Year Built',
            type: 'range',
            min: 1900,
            max: 2010,
            step: 10,
            any: 'Any Year',
            commatize: false
        }
    },

    adapt: function (widget, obj)
    {
        var fieldName = obj.attr('field');

        var field = SpatialMatch.PropertySearch.findField(fieldName);

        if (field != null)
        {
            obj.before('<label class="spatialmatch-widget-search-field-label">' + field.label + '</label>');

            if (field.type == 'text')
            {
                var defval = (widget.attr(fieldName) != null) ? widget.attr(fieldName) : '';

                obj.append('<input type="text" value="' + defval + '" />');
            }
            else if (field.type == 'range')
            {
                var valueId = 'x-' + SpatialMatch.Util.uid();

                obj.before('<label id="' + valueId + '" class="spatialmatch-widget-search-field-range-value"></label><br />');

                var min = (field.min != null) ? field.min : 0;
                var max = (field.max != null) ? field.max : min;

                if (min > max)
                {
                    var t = min;
                    max = min;
                    min = t;
                }

                var low = (field.low != null) ? field.low : field.min;
                var high = (field.high != null) ? field.high : field.max;

                if (low < min)
                {
                    low = min;
                }

                if (high > max)
                {
                    high = max;
                }

                if (low > high)
                {
                    var t = low;
                    low = high;
                    high = t;
                }

                var formatter = (field.formatter != null) ? field.formatter : SpatialMatch.PropertySearch.Formatters.basic;

                jQuery('#' + valueId).html(formatter(field, low, high));

                var step = (field.step != null) ? field.step : 1;

                obj.slider(
                {
                    range: true,
                    min: min,
                    max: max,
                    step: step,
                    values: [low, high]
                });

                obj.on('slide slidechange', function (event, ui)
                {
                    jQuery('#' + valueId).html(formatter(field, ui.values[0], ui.values[1]));
                });
            }
        }
    },

    run: function (widget)
    {
        var license = jQuery.trim(widget.attr('license'));

        var listingType = jQuery.trim(widget.attr('listingType'));

        var location = jQuery.trim(widget.attr('location'));

        var zoom = jQuery.trim(widget.attr('zoom'));

        zoom = (SpatialMatch.String.isNumber(zoom)) ? parseInt(zoom) : 13;

        var bookmarks =
        {
            'app:homes.panel': 'open'
        };

        if ((listingType != null) && (listingType.length > 0))
        {
            bookmarks['layer:' + listingType + '.toggle'] = 'on';
        }

        if ((location != null) && (location.length > 0))
        {
            bookmarks['map.goto'] = location;
        }

        if ((zoom != null) && (zoom > 0))
        {
            bookmarks['map.zoom'] = zoom;
        }

        var objs = widget.find('.spatialmatch-widget-search-field');

        if ((objs == null) || (objs.length == 0))
        {
            return;
        }

        var valid = true;

        objs.each(function (index, obj)
        {
            obj = jQuery(obj);

            var fieldName = obj.attr('field');

            var field = SpatialMatch.PropertySearch.findField(fieldName);

            if (field != null)
            {
                if (field.type == 'text')
                {
                    var input = obj.children('input');

                    var value = input.val();

                    if ((value != null) && (value.length > 0))
                    {
                        if (field.automation != null)
                        {
                            bookmarks[field.automation] = value;
                        }
                        else
                        {
                            bookmarks['app:homes:filter.' + fieldName] = value;
                        }
                    }
                    else if (field.required == true)
                    {
                        if (valid == true)
                        {
                            input.css('background-color', '#FFFACD');

                            alert('Please specify a valid value: ' + field.label);

                            valid = false;
                        }
                    }
                }
                else if (field.type == 'range')
                {
                    var low = obj.slider('values', 0);
                    var high = obj.slider('values', 1);

                    if (low != field.min)
                    {
                        bookmarks['app:homes:filter.' + fieldName + 'Min'] =
                            SpatialMatch.PropertySearch.valueOf(field, low);
                    }

                    if (high != field.max)
                    {
                        bookmarks['app:homes:filter.' + fieldName + 'Max'] =
                            SpatialMatch.PropertySearch.valueOf(field, high);
                    }
                }
            }
        });

        if (valid == true)
        {
            var target = widget.attr('target').split('|');

            if ((target != null) && (target.length > 0))
            {
                var options = (target.length > 1) ? eval('(' + target[1] + ')') : { };

                if (target[0] == 'popup')
                {
                    SpatialMatch.SearchUtils.popup(widget, license, SpatialMatch.String.join(bookmarks, ';'), options);
                }
                else if (target[0] == 'page')
                {
                    SpatialMatch.SearchUtils.page(widget, SpatialMatch.String.join(bookmarks, '&'), options);
                }
            }
        }
    },

    reset: function (widget)
    {
        var objs = widget.find('.spatialmatch-widget-search-field');

        if ((objs == null) || (objs.length == 0))
        {
            return;
        }

        objs.each(function (index, obj)
        {
            obj = jQuery(obj);

            var fieldName = obj.attr('field');

            var field = SpatialMatch.PropertySearch.findField(fieldName);

            if (field != null)
            {
                if (field.type == 'text')
                {
                    var defval = widget.attr(fieldName);

                    obj.children('input').val((defval != null) ? defval : '');
                }
                else if (field.type == 'range')
                {
                    var min = obj.slider('option', 'min');
                    var max = obj.slider('option', 'max');

                    obj.slider('values', 0, min);
                    obj.slider('values', 1, max);
                }
            }
        });
    },

    findField: function(fieldName)
    {
        var field = null;

        if (SpatialMatch.PropertySearch.Fields != null)
        {
            var fields = SpatialMatch.PropertySearch.Fields;

            if ((fieldName != null) && (fields[fieldName] != null))
            {
                field = fields[fieldName];
            }
        }

        return field;
    },

    valueOf: function (field, value)
    {
        if (jQuery.isArray(field.mappings))
        {
            if (value < 0)
            {
                value = 0;
            }
            else if (value >= field.mappings.length)
            {
                value = field.mappings.length - 1;
            }

            value = field.mappings[value];
        }

        return value;
    }
}

SpatialMatch.LifestyleSearch =
{
    Categories:
    {
        book_stores:
        {
            label: 'Book Stores',
            layers: []
        },

        casual_dining:
        {
            label: 'Casual Dining',
            layers: ['causal_dining']
        },

        coffee_shops:
        {
            label: 'Coffee Shops',
            layers: ['coffee_shops']
        },

        grocery_stores:
        {
            label: 'Grocery Stores',
            layers: ['grocery']
        },

        golf_courses:
        {
            label: 'Golf Courses',
            layers: ['golf_courses']
        },

        hospitals:
        {
            label: 'Hospitals',
            layers: ['hospitals']
        },

        hotels:
        {
            label: 'Hotels',
            layers: ['hotels']
        },

        movie_theaters:
        {
            label: 'Movie Theaters',
            layers: ['movie_theaters']
        },

        private_schools:
        {
            label: 'Private Schools',
            layers: ['elementary_schools_private', 'middle_schools_private', 'high_schools_private']
        },

        public_schools:
        {
            label: 'Public Schools',
            layers: ['elementary_schools_public', 'middle_schools_public', 'high_schools_public']
        }
    },

    adapt: function (widget, obj)
    {
        var categoryName = obj.attr('category');

        var category = SpatialMatch.LifestyleSearch.findCategory(categoryName);

        if (category != null)
        {
            var id = obj.attr('id');

            obj.after('<label class="spatialmatch-widget-search-field-label" for="' + id + '">&nbsp;' + category.label + '</label>');
        }
    },

    run: function (widget)
    {
        var uid = widget.attr('uid');

        var license = jQuery.trim(widget.attr('license'));

        var bookmarks = { };

        var textbox = jQuery('#' + uid + '-location');

        var location = textbox.val();

        if ((location != null) && (location.length > 0))
        {
            bookmarks['map.goto'] = location;
        }
        else
        {
            textbox.css('background-color', '#FFFACD');

            alert('Please specify a lifestyle search location.');

            return;
        }

        var zoom = jQuery.trim(widget.attr('zoom'));

        zoom = (SpatialMatch.String.isNumber(zoom)) ? parseInt(zoom) : 13;

        if ((zoom != null) && (zoom > 0))
        {
            bookmarks['map.zoom'] = zoom;
        }

        var selection = false;

        widget.find('.spatialmatch-widget-lifestyle-category').each (function (index, obj)
        {
            obj = jQuery(obj);

            if (jQuery(obj).is(':checked'))
            {
                var categoryName = obj.attr('category');

                var category = SpatialMatch.LifestyleSearch.findCategory(categoryName);

                if (category != null)
                {
                    selection = true;

                    if ((category.layers != null) && (category.layers.length > 0))
                    {
                        for (var ii = 0; ii < category.layers.length; ii++)
                        {
                            var layer = category.layers[ii];

                            bookmarks['layer:lifestyle:' + layer + '.toggle'] = 'on';
                        }
                    }
                }
            }
        });

        if (selection == true)
        {
            var target = widget.attr('target').split('|');

            if ((target != null) && (target.length > 0))
            {
                var options = (target.length > 1) ? eval('(' + target[1] + ')') : { };

                if (target[0] == 'popup')
                {
                    SpatialMatch.SearchUtils.popup(widget, license, SpatialMatch.String.join(bookmarks, ';'), options);
                }
                else if (target[0] == 'page')
                {
                    SpatialMatch.SearchUtils.page(widget, SpatialMatch.String.join(bookmarks, '&'), options);
                }
            }
        }
        else
        {
            alert('Please select at least one lifestyle category.');
        }
    },

    reset: function (widget)
    {
        var uid = widget.attr('uid');

        jQuery('#' + uid + '-location').val(widget.attr('location'));

        widget.find('.spatialmatch-widget-lifestyle-category').each(function (index, obj)
        {
            jQuery(obj).removeAttr('checked');
        });
    },

    findCategory: function(categoryName)
    {
        var category = null;

        if ((categoryName != null) && (SpatialMatch.LifestyleSearch.Categories[categoryName] != null))
        {
            var category = SpatialMatch.LifestyleSearch.Categories[categoryName];
        }

        return category;
    }
}

SpatialMatch.Event =
{
    addListener: function (id, eventName, callback)
    {
        if (SpatialMatch.Event._listeners == null)
        {
            SpatialMatch.Event._listeners = [];
        }

        var listeners = SpatialMatch.Event._listeners;

        if (listeners[id] == null)
        {
            listeners[id] = { };
        }

        if (listeners[id][eventName] == null)
        {
            listeners[id][eventName] = [];
        }

        listeners[id][eventName].push(callback);
    },

    dispatchEvent: function (id, eventName, data)
    {
        var listeners = SpatialMatch.Event._listeners;

        if ((listeners != null) && (listeners[id] != null) && (listeners[id][eventName] != null))
        {
            var l = listeners[id][eventName];

            for (var ii = 0; ii < l.length; ii++)
            {
                l[ii].call(null, eventName, data);
            }
        }
    },

    _listeners: null
}

SpatialMatch.Facebook =
{
    authorize: function (url)
    {
        var w = window.open(url, "__facebook", "width=600,height=400,scrollbars=no,toolbar=no,status=no,location=no,menubar=no,resizable=no");

        if (w == null)
        {
            return false;
        }

        w.focus();

        return true;
    },

    like: function ()
    {
        var url = "http://www.facebook.com/plugins/likebox.php?href=http%3A%2F%2Fwww.facebook.com%2Fspatialmatch&colorscheme=light&show_faces=false&stream=false&header=false&width=250&height=60";

        var w = window.open(url, "__like", "width=250,height=60,scrollbars=no,toolbar=no,status=no,location=no,menubar=no,resizable=no");

        if (w == null)
        {
            return false;
        }

        w.focus();

        return true;
    },

    share: function (data)
    {
        if (window.FB != null)
        {
            FB.ui(data, function() { });

            return true;
        }

        return false;
    }
}

jQuery(document).ready(function()
{
    jQuery('.spatialmatch-widget-property-search').each(function (index, widget)
    {
        widget = jQuery(widget);

        widget.find('.spatialmatch-widget-search-field').each(function (index, field)
        {
            SpatialMatch.PropertySearch.adapt(widget, jQuery(field));
        });

        widget.find('.button[action="search"]').each(function (index, button)
        {
            jQuery(button).click(function (event)
            {
                SpatialMatch.PropertySearch.run(widget);
            });
        });

        widget.find('.button[action="reset"]').each(function (index, button)
        {
            jQuery(button).click(function (event)
            {
                SpatialMatch.PropertySearch.reset(widget);
            });
        });
    });

    jQuery('.spatialmatch-widget-lifestyle-search').each(function (index, widget)
    {
        widget = jQuery(widget);

        widget.find('.spatialmatch-widget-lifestyle-category').each(function (index, category)
        {
            SpatialMatch.LifestyleSearch.adapt(widget, jQuery(category));
        });

        widget.find('.button[action="search"]').each(function (index, button)
        {
            jQuery(button).click(function (event)
            {
                SpatialMatch.LifestyleSearch.run(widget);
            });
        });

        widget.find('.button[action="reset"]').each(function (index, button)
        {
            jQuery(button).click(function (event)
            {
                SpatialMatch.LifestyleSearch.reset(widget);
            });
        });
    });

    var fb = document.getElementById('fb-root');

    if (fb != null)
    {
        var s = document.createElement('script');

        s.src = document.location.protocol + '//connect.facebook.net/en_US/all.js';

        fb.appendChild(s);
    }

    var popup = SpatialMatch.Util.getParameter('sm.popup');

    if ((popup != null) && (popup.length > 0))
    {
        if (SpatialMatch.String.toBoolean(popup) == true)
        {
            var links = jQuery('.spatialmatch-popup-wrapper A');

            if (links.length > 0)
            {
                links[0].click();
            }
        }
    }
});

document.write('<div id="fb-root"></div>');

window.fbAsyncInit = function()
{
    FB.init({appId: '149037818463361', status: true, cookie: true});
};
