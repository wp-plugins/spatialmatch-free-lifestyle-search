SpatialMatch.Admin =
{
    isLicenseKeyValid: function (value)
    {
        var self = SpatialMatch.Admin;
        
        if (self.licenseKeyRegex == null)
        {
            self.licenseKeyRegex = /^[A-Z0-9]{4}\-[A-Z0-9]{4}\-[A-Z0-9]{4}(\-[A-Z0-9]{4})?$/;
        }
            
        return self.licenseKeyRegex.test(jQuery.trim(value));
    },

    Widget:
    {
        targetChangeHandler: function (obj)
        {
            var select = jQuery(obj);
            
            var id = select.attr('id');

            var value = select.val();

            jQuery('#' + id + '-popup').css('display', (value == 'popup') ? 'block' : 'none');
            jQuery('#' + id + '-page').css('display', (value == 'page') ? 'block' : 'none');
        },
        
        listingTypeChangeHandler: function (obj)
        {
            var select = jQuery(obj);
            
            var id = select.attr('id');

            var value = select.val();
            
            var items = select.parents('div.spatialmatch-search-form').find('fieldset div ul li');

            for (var ii = 0; ii < items.length; ii++)            
            {
                var item = jQuery(items[ii]);

                var listingTypes = item.attr('listingTypes');
                
                if (listingTypes.indexOf('*') == -1)
                {
                    item.css('display', (listingTypes.indexOf(value) != -1) ? 'block' : 'none');
                }
            }
        },
        
        orientationChangeHandler: function (obj)
        {
            var select = jQuery(obj);
            
            var id = select.attr('id');

            var value = select.val();

            jQuery('#' + id + '-horizontal').css('display', (value == 'horizontal') ? 'block' : 'none');
        }        
    }
}
