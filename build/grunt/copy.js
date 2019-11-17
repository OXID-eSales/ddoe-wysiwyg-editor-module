module.exports = {

	fonts: {
		files: [
			{
				expand: true,
				src: '*',
				cwd: 'node_modules/font-awesome/fonts/',
				dest: 'out/src/fonts/'
			},
			{
				expand: true,
				src: '*',
				cwd: 'node_modules/summernote/dist/font/',
				dest: 'out/src/fonts/'
			}
		]
	}
};
