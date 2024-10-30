=== Infinity Quiz ===
Contributors: akash112
Tags: quiz,buzzfeed,test,score,post
Donate link: https://www.paypal.me/AkashSaggar
Requires at least: 4.0
Tested up to: 4.8.2
Requires PHP: 5.0
Stable tag: 1.0.4
License: GPL2
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Easily create dynamic quizzes with customised responses. Creating your own personality or category type quizzes has never been easier.

== Description ==
Have you ever wanted to take on the likes of BuzzFeed with your own personality/category tests? Well now you can!

With Infinity Quiz, you can create your own custom quizzes easily and effortlessly.
Let Infinity Quiz take care of the complicated calculations involved with making these kinds of quizzes, while you enjoy the views and build your mailing list. Infinity Quiz allows you to require the users name and email before they proceed, and can even email them their quiz results, encouraging them to come back.

Never has there been such an effective way to attract the millennials to your site until now!

== Installation ==
Install the plugin through the normal WordPress plugins menu.
Then you will see a new item in your left-side admin menu called "Quizzes".
From there you can create new quizzes and manage existing ones.

You can view quizzes on their own, or add them to any post with the simple shortcode:
[infinity-quiz quiz="your-quiz-ID"]

== Frequently Asked Questions ==
Q: How do I add questions?
A: Questions are formatted in the following way: question,category
For example: Do you like flying carpets?,Aladdin
First you write your question, then the category that question should give points to, separated by a comma.

Q: How can I add a quiz into another page/post?
A: While editing a quiz, scroll to the bottom of the page and you will see the shortcode. Copy and paste this shortcode into any page/post, and your quiz will appear. Shortcodes are in the form [infinity-quiz quiz="your-quiz-ID"], where "your-quiz-ID" is what comes in the address bar when your quiz is open.

Q: Can I customise the visual style of the quiz?
A: At the moment, this can be achieved only by directly editing the "/wp-content/infinity-quiz/style.css" file. Full customisation will be added soon.

Q: Where are the users names/emails that I have collected?
A: Currently, they are not stored anywhere. As long as you chose to have quiz results emailed to you, the users names/emails would have been included in those emails. Storing user details may come in the future.

== Changelog ==
1.0.4
Users can now have commas in their question too. The last comma will be used as the separator between the question and category.

1.0.3
Multiple emails can now be set to receive results.

1.0.2
Fixed email content.

1.0.1
Fixed bug where user could go to the next question, even if they did not answer the current one.

1.0.0
Initial release.