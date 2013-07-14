<h1>PHP.Simple Code Highlight</h1>
<p>
    Some PHP to perform some simple syntax highlighting. My main goals were to keep it "to the point" and server side. I did this fast
    so there may be aspects that need to be fixed, feel free to help me out.
</p>



<h2>How it works</h2>
<p>
    Initiate the Simple Code Highlight class, point it at the url or file you want and let it do its job.
    It wraps bits of HTML around your syntax and returns an object that contains an array of parsed HTML (as lines of code), the
    raw data, and a few bits about caching (if you decided to use that aspect).
</p>
<p>
    There are several parameters you can use to refine your use of the class, the most prominent being the use of a cache. The parameters
    you can use to refine your use of this aspect are annotated in code.
</p>
<p>
    If you decide to include your own filters/regex patterns the things to look out for are ORDER, ORDER, ORDER. The reason, I provided a
    built in conditional that prevents nested labeling. So if you start small you may prevent a large match and vice-versa. With the provided
    filters I tried to keep things simple and generic as my own purposes were pretty minimal (JS, HTML, CSS).
</p>



<h2>Browser compatibility</h2>
<p>
    Server side, so no real worries. However in the demo I did make use of some CSS that may no be fully compatible with older versions of IE.
</p>



<h2>License</h2>
<p>
    My aspect is released under the <a href="http://opensource.org/licenses/mit-license.php">MIT License</a>.
</p>
<p>
    I did include <a href="http://necolas.github.com/normalize.css">Normalize.css</a> and the box model tweak from
    <a href="http://www.paulirish.com/2012/box-sizing-border-box-ftw/">Paul Irish</a> for general demo formatting purposes.
</p>
<p>
    As well, I also included regular expression patterns I pieced together from all over. I'm not super keen on using them, but they have their purpose.
</p>
