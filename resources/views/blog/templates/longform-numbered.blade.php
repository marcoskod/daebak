<article class="post post--numbered" itemscope itemtype="https://schema.org/Article">
@include('blog.templates._head')
@include('blog.templates._cover')
    <div class="post-layout">
@include('blog.templates._toc')
@include('blog.templates._body', ['wrapSections' => true])
    </div>
</article>
