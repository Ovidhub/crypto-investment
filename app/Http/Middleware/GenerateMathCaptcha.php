<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class GenerateMathCaptcha
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Only generate new question on GET requests (viewing the form)
        // Don't regenerate on POST (form submission) to preserve the answer for validation
        if ($request->isMethod('get')) {
            // Generate two random numbers between 1 and 10
            $num1 = rand(1, 10);
            $num2 = rand(1, 10);
            
            // Store the numbers and correct answer in session
            session([
                'math_captcha_num1' => $num1,
                'math_captcha_num2' => $num2,
                'math_captcha_answer' => $num1 + $num2
            ]);
        }
        
        // Always share the question with the view (from session)
        $num1 = session('math_captcha_num1', 0);
        $num2 = session('math_captcha_num2', 0);
        
        view()->share([
            'math_num1' => $num1,
            'math_num2' => $num2,
            'math_question' => "What is {$num1} + {$num2}?"
        ]);

        return $next($request);
    }
}
