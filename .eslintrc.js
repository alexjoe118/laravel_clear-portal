module.exports = {
	env: {
		browser: true,
		commonjs: true,
		es6: true,
		node: true,
		jquery: true,
	},
	extends: ['eslint:recommended', 'wordpress'],
	parserOptions: {
		sourceType: 'module',
		ecmaVersion: 9
	},
	rules: {
		yoda: 'off',
		curly: 'off',
		indent: ['error', 'tab'],
		'linebreak-style': ['error', 'unix'],
		quotes: ['error', 'single'],
		semi: ['error', 'always'],
		'no-console': 'off',
		'no-unreachable': 'off',
		'space-before-function-paren': ['error', 'always']
	},
};
