module.exports = {

	summernotefontfix: {
		options: {
			patterns: [
				{ match: './font/summernote.woff', replacement: './../fonts/summernote.woff' }
			],
			usePrefix: false
		},
		files: [
			{src: ["out/src/css/backend.min.css"], dest: "out/src/css/backend.min.css"}
		]
	}
}
