(function($)
{
	var state = {};
	if (/^\{.*\}$/.test(window.name))
	{
		state = JSON.parse(window.name);
	}
	else
	{
		window.name = '{}';
	}

	$.namesession = {
		'set': function(n, v)
		{
			state[n] = v;
			window.name = JSON.stringify(state);
		},
		'get': function(n, dv)
		{
			if (n in state)
			{
				return state[n];
			}
			return dv;
		}
	};

})(jQuery);
