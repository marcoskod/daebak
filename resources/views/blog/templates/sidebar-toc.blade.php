<article class="post post--sidebar-toc" itemscope itemtype="https://schema.org/Article">
    <div class="post-layout">
        <div class="post-main">
@include('blog.templates._head')
@include('blog.templates._cover')
@include('blog.templates._body')
        </div>
@include('blog.templates._toc', ['tocClass' => ' post-toc--rail'])
    </div>
</article>
