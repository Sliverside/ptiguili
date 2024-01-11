const Flashes = function(id) {
  const root = document.getElementById(id)
  if(!root) {
    console.error('no element with the id: ' + id)
    return null
  }
  const flashes = root.querySelectorAll('.flash')
  const desapearDelay = 6000;
  const desapearGap = 1000;
  const desapearDuration = 1000;

  if(flashes.length === 0) return null

  root.classList.add('active')

  for (let i = 0; i < flashes.length; i++) {
    const flash = flashes[flashes.length - i - 1];
    const animation = flash.animate(
      [{opacity: 0}],
      {
        duration: desapearDuration,
        delay: desapearDelay + desapearGap * (i)
      }
    )
    animation.onfinish = () => {
      flash.style.display = 'none';
    };
  }
}


export {
  Flashes
}
