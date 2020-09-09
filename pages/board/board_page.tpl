<html>
	<head>
		<link rel="stylesheet" type="text/css" href="/pages/css/global.css" />
		<link rel="stylesheet" type="text/css" href="/pages/css/anonsaba_board.css" />
		<link rel="apple-touch-icon" sizes="180x180" href="/pages/images/apple-touch-icon.png">
		<link rel="icon" type="image/png" sizes="32x32" href="/pages/images/favicon-32x32.png">
		<link rel="icon" type="image/png" sizes="16x16" href="/pages/images/favicon-16x16.png">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<meta http-equiv="Pragma" content="no-cache" />
		<meta http-equiv="cache-control" content="no-cache" />
		<meta http-equiv="expires" content="Sat, 17 Mar 1990 00:00:01 GMT" />
		<title>{{board.desc}}</title>
	</head>
	<body>
		<div class="navbar" id="navbar">
			[<a href="{{weburl}}">Home</a>]&nbsp;&nbsp;
			{% for sect in boards %}
				[
				{% for brd in sect %}
					<a title="{{brd.desc}}" href="{{weburl}}{{brd.name}}/">{{brd.desc}}</a>{% if loop.last %}{% else %} / {% endif %}
				{% endfor %}
				 ]
			{% endfor %}
		</div>
		<div class="boardname" id="boardname">
			/{{board.name}}/ - {{board.desc}}
		</div>
	</body>
</html>