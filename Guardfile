guard :phpunit2, :all_on_start => false, :tests_path => 'tests/', :cli => '--colors -c build/phpunit.xml' do
	# Run any test in app/tests upon save.
	watch(%r{^.+Test\.php$})

	# When a file is edited, try to run its associated test.
	watch(%r{^src/Orchestra/Html/(.+)\.php$}) { |m| "tests/#{m[1]}Test.php"}
end
