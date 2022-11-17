import React, { Component } from 'react'
import WaveSurfer from 'wavesurfer.js';
import ReactWaves from '@dschoon/react-waves';
//import { WaveformContianer, Wave, PlayButton } from './Waveform.styled';

export default class WaveForm extends Component {
    constructor(props){
        super(props);
        this.state={
            playing: false
        }
    }
    componentDidMount(){
   
    }
    render() {
        const url = 'https://www.mfiles.co.uk/mp3-downloads/gs-cd-track2.mp3';
        return (
            <div className={'container example'}>
            <div className="play button" onClick={() => { this.setState({ playing: !this.state.playing }) }}>
              { !this.state.playing ? '▶' : '■' }
            </div>
            <ReactWaves
              audioFile={url}
              className={'react-waves'}
              options={{
                barHeight: 2,
                cursorWidth: 0,
                height: 200,
                hideScrollbar: true,
                progressColor: '#EC407A',
                responsive: true,
                waveColor: '#D1D6DA',
              }}
              volume={1}
              zoom={1}
              playing={this.state.playing}
            />
          </div>
        )
    }
}
